<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Models\Guest;
use App\Models\HotelSetting;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Service;
use App\Models\ServiceLine;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingConfirmed;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use RuntimeException;

class ReservationService
{
    public function __construct(
        protected AvailabilityService $availability,
    ) {}

    /**
     * Price quote for a stay.
     *
     * @return array{nights: int, nightly_rate: float, subtotal: float, tax_rate: float, tax_amount: float, total: float, currency: string}
     */
    public function quote(RoomType $roomType, string $checkIn, string $checkOut): array
    {
        $nights = max(1, (int) Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)));
        $nightlyRate = (float) $roomType->base_price;
        $subtotal = round($nightlyRate * $nights, 2);
        $taxRate = HotelSetting::taxRate();
        $taxAmount = round($subtotal * $taxRate / 100, 2);

        return [
            'nights' => $nights,
            'nightly_rate' => $nightlyRate,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total' => round($subtotal + $taxAmount, 2),
            'currency' => HotelSetting::currency(),
        ];
    }

    /**
     * Create a booking. Locks room inventory inside a transaction to
     * guarantee no double-booking, assigns a room, prices the stay,
     * and queues the confirmation notification.
     *
     * @param array{
     *     room_type_id: int, check_in: string, check_out: string,
     *     adults: int, children?: int, special_requests?: string|null,
     *     first_name: string, last_name: string, email: string, phone?: string|null,
     *     user_id?: int|null, source?: string, status?: ReservationStatus,
     * } $data
     */
    public function create(array $data): Reservation
    {
        $roomType = RoomType::query()->findOrFail($data['room_type_id']);

        $reservation = DB::transaction(function () use ($roomType, $data) {
            $room = $this->availability->lockAvailableRoom(
                $roomType,
                $data['check_in'],
                $data['check_out'],
            );

            if (! $room) {
                throw new RuntimeException(
                    "No {$roomType->name} rooms are available for the selected dates.",
                );
            }

            $guest = Guest::query()->firstOrCreate(
                ['email' => strtolower(trim($data['email']))],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'] ?? null,
                    'user_id' => $data['user_id'] ?? null,
                ],
            );

            if (($data['user_id'] ?? null) && ! $guest->user_id) {
                $guest->update(['user_id' => $data['user_id']]);
            }

            $quote = $this->quote($roomType, $data['check_in'], $data['check_out']);

            return Reservation::query()->create([
                'code' => Reservation::generateCode(),
                'room_type_id' => $roomType->id,
                'room_id' => $room->id,
                'guest_id' => $guest->id,
                'user_id' => $data['user_id'] ?? null,
                'check_in' => $data['check_in'],
                'check_out' => $data['check_out'],
                'adults' => $data['adults'],
                'children' => $data['children'] ?? 0,
                'status' => $data['status'] ?? ReservationStatus::Pending,
                'source' => $data['source'] ?? 'online',
                'special_requests' => $data['special_requests'] ?? null,
                'nightly_rate' => $quote['nightly_rate'],
                'subtotal' => $quote['subtotal'],
                'tax_amount' => $quote['tax_amount'],
                'total_amount' => $quote['total'],
                'currency' => $quote['currency'],
            ]);
        });

        $this->notifyGuest($reservation, new BookingConfirmed($reservation));

        return $reservation;
    }

    public function confirm(Reservation $reservation): Reservation
    {
        $reservation->update(['status' => ReservationStatus::Confirmed]);

        return $reservation;
    }

    public function cancel(Reservation $reservation): Reservation
    {
        if (! $reservation->canBeCancelled()) {
            throw new RuntimeException('This reservation can no longer be cancelled.');
        }

        $reservation->update([
            'status' => ReservationStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

        $this->notifyGuest($reservation, new BookingCancelled($reservation));

        return $reservation;
    }

    public function checkIn(Reservation $reservation): Reservation
    {
        if (! $reservation->canCheckIn()) {
            throw new RuntimeException('This reservation cannot be checked in.');
        }

        DB::transaction(function () use ($reservation) {
            $reservation->update([
                'status' => ReservationStatus::CheckedIn,
                'checked_in_at' => now(),
            ]);

            $reservation->room?->update(['status' => RoomStatus::Occupied]);
        });

        return $reservation;
    }

    public function checkOut(Reservation $reservation): Reservation
    {
        if (! $reservation->canCheckOut()) {
            throw new RuntimeException('This reservation cannot be checked out.');
        }

        DB::transaction(function () use ($reservation) {
            $reservation->update([
                'status' => ReservationStatus::CheckedOut,
                'checked_out_at' => now(),
            ]);

            $reservation->room?->update(['status' => RoomStatus::Cleaning]);
        });

        return $reservation;
    }

    public function markNoShow(Reservation $reservation): Reservation
    {
        $reservation->update(['status' => ReservationStatus::NoShow]);

        return $reservation;
    }

    /**
     * Update stay details, room assignment, and pricing.
     * Re-validates availability whenever dates, room type, or room change.
     */
    public function update(Reservation $reservation, array $data): Reservation
    {
        return DB::transaction(function () use ($reservation, $data) {
            $roomType = RoomType::query()->findOrFail($data['room_type_id'] ?? $reservation->room_type_id);
            $checkIn = $data['check_in'] ?? $reservation->check_in->toDateString();
            $checkOut = $data['check_out'] ?? $reservation->check_out->toDateString();

            $room = null;

            if (! empty($data['room_id'])) {
                $room = Room::query()
                    ->where('room_type_id', $roomType->id)
                    ->findOrFail($data['room_id']);
            } elseif ($reservation->room && $reservation->room->room_type_id === $roomType->id) {
                $room = $reservation->room;
            }

            if ($room && ! $this->availability->isRoomAvailable($room, $checkIn, $checkOut, $reservation->id)) {
                throw new RuntimeException("Room {$room->room_number} is not available for those dates.");
            }

            if (! $room) {
                $room = $this->availability->lockAvailableRoom($roomType, $checkIn, $checkOut);

                if (! $room) {
                    throw new RuntimeException("No {$roomType->name} rooms are available for the selected dates.");
                }
            }

            $reservation->fill([
                'room_type_id' => $roomType->id,
                'room_id' => $room->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'adults' => $data['adults'] ?? $reservation->adults,
                'children' => $data['children'] ?? $reservation->children,
                'nightly_rate' => $data['nightly_rate'] ?? $reservation->nightly_rate,
                'discount_amount' => $data['discount_amount'] ?? $reservation->discount_amount ?? 0,
                'special_requests' => $data['special_requests'] ?? $reservation->special_requests,
            ])->save();

            return $this->reprice($reservation);
        });
    }

    /**
     * Recompute subtotal, services, tax, and total from current data.
     * Total = room subtotal + services − discount + tax on the net amount.
     */
    public function reprice(Reservation $reservation): Reservation
    {
        $reservation->refresh()->loadMissing('serviceLines');

        $subtotal = round((float) $reservation->nightly_rate * $reservation->nights, 2);
        $servicesAmount = round((float) $reservation->serviceLines->sum('amount'), 2);
        $discount = round(min((float) $reservation->discount_amount, $subtotal + $servicesAmount), 2);
        $taxAmount = round(($subtotal + $servicesAmount - $discount) * HotelSetting::taxRate() / 100, 2);

        $reservation->forceFill([
            'subtotal' => $subtotal,
            'services_amount' => $servicesAmount,
            'discount_amount' => $discount,
            'tax_amount' => $taxAmount,
            'total_amount' => round($subtotal + $servicesAmount - $discount + $taxAmount, 2),
        ])->save();

        return $reservation;
    }

    /**
     * Add a chargeable hotel service to the folio (price snapshot).
     */
    public function addService(Reservation $reservation, Service $service, int $quantity = 1): Reservation
    {
        $reservation->serviceLines()->create([
            'service_id' => $service->id,
            'name' => $service->name,
            'unit_price' => $service->price,
            'quantity' => $quantity,
            'amount' => round((float) $service->price * $quantity, 2),
        ]);

        return $this->reprice($reservation);
    }

    public function removeService(Reservation $reservation, ServiceLine $line): Reservation
    {
        if ($line->reservation_id !== $reservation->id) {
            throw new RuntimeException('This charge does not belong to the reservation.');
        }

        $line->delete();

        return $this->reprice($reservation);
    }

    /**
     * Soft-delete a reservation, freeing its room inventory.
     */
    public function delete(Reservation $reservation): void
    {
        if ($reservation->status === ReservationStatus::CheckedIn) {
            throw new RuntimeException('Check the guest out before deleting this reservation.');
        }

        $reservation->delete();
    }

    protected function notifyGuest(Reservation $reservation, object $notification): void
    {
        $email = $reservation->guest?->email;

        if ($email) {
            Notification::route('mail', $email)->notify($notification);
        }
    }
}

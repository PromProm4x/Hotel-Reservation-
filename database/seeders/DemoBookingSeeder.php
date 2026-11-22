<?php

namespace Database\Seeders;

use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Enums\RoomStatus;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\AvailabilityService;
use Illuminate\Database\Seeder;

class DemoBookingSeeder extends Seeder
{
    public function run(): void
    {
        $availability = app(AvailabilityService::class);
        $guests = Guest::factory()->count(12)->create();

        $scenarios = [
            // [check_in offset days, nights, status, paid ratio]
            [-10, 3, ReservationStatus::CheckedOut, 1.0],
            [-7, 2, ReservationStatus::CheckedOut, 1.0],
            [-5, 4, ReservationStatus::CheckedOut, 1.0],
            [-2, 5, ReservationStatus::CheckedIn, 0.5],
            [-1, 3, ReservationStatus::CheckedIn, 1.0],
            [0, 2, ReservationStatus::Confirmed, 0.5],
            [0, 4, ReservationStatus::Confirmed, 0.0],
            [1, 3, ReservationStatus::Confirmed, 0.5],
            [2, 2, ReservationStatus::Pending, 0.0],
            [4, 5, ReservationStatus::Confirmed, 0.5],
            [7, 3, ReservationStatus::Pending, 0.0],
            [10, 2, ReservationStatus::Confirmed, 1.0],
            [-4, 2, ReservationStatus::Cancelled, 0.0],
            [-3, 1, ReservationStatus::NoShow, 0.0],
        ];

        foreach ($scenarios as $index => [$offset, $nights, $status, $paidRatio]) {
            $checkIn = now()->addDays($offset)->startOfDay();
            $checkOut = $checkIn->copy()->addDays($nights);
            $guest = $guests[$index % $guests->count()];

            // Find a room that is genuinely free for these dates (for blocking statuses).
            $room = Room::query()
                ->with('roomType')
                ->get()
                ->first(function (Room $candidate) use ($availability, $checkIn, $checkOut, $status) {
                    if (! in_array($status, ReservationStatus::blocking(), true)) {
                        return true;
                    }

                    return $availability->isRoomAvailable($candidate, $checkIn->toDateString(), $checkOut->toDateString());
                });

            if (! $room) {
                continue;
            }

            $rate = (float) $room->roomType->base_price;
            $subtotal = round($rate * $nights, 2);
            $tax = round($subtotal * 0.10, 2);
            $total = round($subtotal + $tax, 2);

            $reservation = Reservation::query()->create([
                'code' => Reservation::generateCode(),
                'room_type_id' => $room->room_type_id,
                'room_id' => $room->id,
                'guest_id' => $guest->id,
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkOut->toDateString(),
                'adults' => min(2, $room->roomType->capacity_adults),
                'children' => 0,
                'status' => $status,
                'source' => 'online',
                'nightly_rate' => $rate,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'currency' => 'USD',
                'checked_in_at' => $status === ReservationStatus::CheckedIn || $status === ReservationStatus::CheckedOut ? $checkIn : null,
                'checked_out_at' => $status === ReservationStatus::CheckedOut ? $checkOut : null,
                'cancelled_at' => $status === ReservationStatus::Cancelled ? now()->subDays(5) : null,
            ]);

            if ($paidRatio > 0) {
                $reservation->payments()->create([
                    'amount' => round($total * $paidRatio, 2),
                    'currency' => 'USD',
                    'method' => 'card',
                    'type' => $paidRatio >= 1.0 ? 'full' : 'deposit',
                    'status' => PaymentStatus::Completed,
                    'reference' => 'DEMO-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'paid_at' => $checkIn->copy()->subDays(2),
                ]);
            }

            if ($status === ReservationStatus::CheckedIn) {
                $room->update(['status' => RoomStatus::Occupied]);
            }
        }
    }
}

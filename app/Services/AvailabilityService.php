<?php

namespace App\Services;

use App\Enums\RoomStatus;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * Room types with at least one free room for the given stay.
     *
     * @return Collection<int, RoomType> each with an `available_rooms_count` attribute
     */
    public function searchRoomTypes(
        string $checkIn,
        string $checkOut,
        int $guests = 1,
        ?int $roomTypeId = null,
    ): Collection {
        return RoomType::query()
            ->active()
            ->when($roomTypeId, fn ($q) => $q->where('id', $roomTypeId))
            ->whereRaw('(capacity_adults + capacity_children) >= ?', [$guests])
            ->get()
            ->map(function (RoomType $roomType) use ($checkIn, $checkOut) {
                $roomType->setAttribute(
                    'available_rooms_count',
                    $this->availableRoomsQuery($roomType, $checkIn, $checkOut)->count(),
                );

                return $roomType;
            })
            ->filter(fn (RoomType $roomType) => $roomType->available_rooms_count > 0)
            ->values();
    }

    /**
     * First bookable room of the given type for the stay (or null).
     */
    public function findAvailableRoom(RoomType $roomType, string $checkIn, string $checkOut): ?Room
    {
        return $this->availableRoomsQuery($roomType, $checkIn, $checkOut)
            ->orderBy('floor')
            ->orderBy('room_number')
            ->first();
    }

    /**
     * Same as findAvailableRoom but locks candidate rows to prevent
     * concurrent double-booking. Call inside a DB transaction.
     */
    public function lockAvailableRoom(RoomType $roomType, string $checkIn, string $checkOut): ?Room
    {
        return $this->availableRoomsQuery($roomType, $checkIn, $checkOut)
            ->orderBy('floor')
            ->orderBy('room_number')
            ->lockForUpdate()
            ->first();
    }

    public function isRoomAvailable(
        Room $room,
        string $checkIn,
        string $checkOut,
        ?int $ignoreReservationId = null,
    ): bool {
        if ($room->status === RoomStatus::Maintenance) {
            return false;
        }

        return ! Reservation::query()
            ->where('room_id', $room->id)
            ->when($ignoreReservationId, fn ($q) => $q->where('id', '!=', $ignoreReservationId))
            ->blocking()
            ->overlapping($checkIn, $checkOut)
            ->exists();
    }

    /**
     * Occupancy rate (0-100) for a given date.
     */
    public function occupancyRate(\DateTimeInterface|string $date): float
    {
        $date = $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : $date;

        $totalRooms = Room::query()->count();

        if ($totalRooms === 0) {
            return 0.0;
        }

        $occupied = Reservation::query()
            ->blocking()
            ->where('check_in', '<=', $date)
            ->where('check_out', '>', $date)
            ->count();

        return round($occupied / $totalRooms * 100, 1);
    }

    protected function availableRoomsQuery(RoomType $roomType, string $checkIn, string $checkOut)
    {
        return $roomType->rooms()
            ->sellable()
            ->whereDoesntHave('reservations', function ($query) use ($checkIn, $checkOut) {
                $query->blocking()->overlapping($checkIn, $checkOut);
            });
    }
}

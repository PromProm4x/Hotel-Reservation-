<?php

namespace App\Enums;

enum RoomStatus: string
{
    case Available = 'available';
    case Occupied = 'occupied';
    case Maintenance = 'maintenance';
    case Cleaning = 'cleaning';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Occupied => 'Occupied',
            self::Maintenance => 'Maintenance',
            self::Cleaning => 'Cleaning',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Available => 'bg-emerald-100 text-emerald-800',
            self::Occupied => 'bg-sky-100 text-sky-800',
            self::Maintenance => 'bg-rose-100 text-rose-800',
            self::Cleaning => 'bg-amber-100 text-amber-800',
        };
    }

    public function dotClasses(): string
    {
        return match ($this) {
            self::Available => 'bg-emerald-500',
            self::Occupied => 'bg-sky-500',
            self::Maintenance => 'bg-rose-500',
            self::Cleaning => 'bg-amber-400',
        };
    }

    /**
     * Statuses a room can be in and still be sold for future dates.
     *
     * @return array<int, string>
     */
    public static function sellableValues(): array
    {
        return [self::Available->value, self::Occupied->value, self::Cleaning->value];
    }
}

<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case CheckedIn = 'checked_in';
    case CheckedOut = 'checked_out';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';

    /**
     * Statuses that block a room from being booked (occupy inventory).
     *
     * @return array<int, self>
     */
    public static function blocking(): array
    {
        return [self::Pending, self::Confirmed, self::CheckedIn];
    }

    /**
     * @return array<int, string>
     */
    public static function blockingValues(): array
    {
        return array_map(fn (self $status) => $status->value, self::blocking());
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::CheckedIn => 'Checked-in',
            self::CheckedOut => 'Checked-out',
            self::Cancelled => 'Cancelled',
            self::NoShow => 'No-show',
        };
    }

    /**
     * Tailwind utility classes for status badges.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-100 text-amber-800',
            self::Confirmed => 'bg-emerald-100 text-emerald-800',
            self::CheckedIn => 'bg-sky-100 text-sky-800',
            self::CheckedOut => 'bg-stone-200 text-stone-700',
            self::Cancelled => 'bg-rose-100 text-rose-800',
            self::NoShow => 'bg-zinc-800 text-zinc-100',
        };
    }
}

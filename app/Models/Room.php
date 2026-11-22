<?php

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    /** @use HasFactory<\Database\Factories\RoomFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_type_id',
        'room_number',
        'floor',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => RoomStatus::class,
        ];
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * The reservation currently checked in to this room, if any.
     */
    public function currentReservation(): HasOne
    {
        return $this->hasOne(Reservation::class)
            ->where('status', \App\Enums\ReservationStatus::CheckedIn->value)
            ->latestOfMany('checked_in_at');
    }

    public function scopeSellable(Builder $query): Builder
    {
        return $query->whereIn('status', RoomStatus::sellableValues());
    }
}

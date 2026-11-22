<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'room_type_id',
        'room_id',
        'guest_id',
        'user_id',
        'check_in',
        'check_out',
        'adults',
        'children',
        'status',
        'source',
        'special_requests',
        'nightly_rate',
        'subtotal',
        'services_amount',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'currency',
        'checked_in_at',
        'checked_out_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'status' => ReservationStatus::class,
            'nightly_rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'services_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public static function generateCode(): string
    {
        do {
            $code = 'DLR-'.strtoupper(Str::random(6));
        } while (static::withTrashed()->where('code', $code)->exists());

        return $code;
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Billed hotel services (folio charges).
     */
    public function serviceLines(): HasMany
    {
        return $this->hasMany(ServiceLine::class);
    }

    /**
     * Reservations that occupy inventory (pending/confirmed/checked-in).
     */
    public function scopeBlocking(Builder $query): Builder
    {
        return $query->whereIn('status', ReservationStatus::blockingValues());
    }

    /**
     * Reservations whose stay overlaps the given date range.
     */
    public function scopeOverlapping(Builder $query, string $checkIn, string $checkOut): Builder
    {
        return $query->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn);
    }

    public function getNightsAttribute(): int
    {
        return max(1, (int) $this->check_in->diffInDays($this->check_out));
    }

    public function getAmountPaidAttribute(): float
    {
        return (float) $this->payments
            ->where('status', PaymentStatus::Completed)
            ->where('type', '!=', 'refund')
            ->sum('amount');
    }

    public function getAmountRefundedAttribute(): float
    {
        return (float) $this->payments
            ->where('status', PaymentStatus::Refunded)
            ->sum('amount');
    }

    public function getBalanceDueAttribute(): float
    {
        return max(0, (float) $this->total_amount - $this->amount_paid + $this->amount_refunded);
    }

    /**
     * Derived payment status: unpaid, partially_paid, paid, refunded.
     */
    public function getPaymentStatusAttribute(): string
    {
        if ($this->amount_refunded > 0 && $this->amount_paid <= $this->amount_refunded) {
            return 'refunded';
        }

        $netPaid = $this->amount_paid - $this->amount_refunded;

        if ($netPaid <= 0) {
            return 'unpaid';
        }

        return $netPaid >= (float) $this->total_amount ? 'paid' : 'partially_paid';
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Paid',
            'partially_paid' => 'Partially Paid',
            'refunded' => 'Refunded',
            default => 'Unpaid',
        };
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [ReservationStatus::Pending, ReservationStatus::Confirmed], true);
    }

    public function canCheckIn(): bool
    {
        return $this->status === ReservationStatus::Confirmed
            || $this->status === ReservationStatus::Pending;
    }

    public function canCheckOut(): bool
    {
        return $this->status === ReservationStatus::CheckedIn;
    }
}

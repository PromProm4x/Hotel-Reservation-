<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory, SoftDeletes;

    public const METHODS = ['cash', 'khqr', 'card', 'bank_transfer', 'stripe', 'paypal'];

    public const METHOD_LABELS = [
        'cash' => 'Cash',
        'khqr' => 'KHQR (Bakong)',
        'card' => 'Card',
        'bank_transfer' => 'Bank transfer',
        'stripe' => 'Stripe',
        'paypal' => 'PayPal',
    ];

    public const TYPES = ['deposit', 'balance', 'full', 'refund'];

    protected $fillable = [
        'reservation_id',
        'recorded_by',
        'amount',
        'currency',
        'method',
        'type',
        'status',
        'reference',
        'notes',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getMethodLabelAttribute(): string
    {
        return self::METHOD_LABELS[$this->method] ?? ucfirst(str_replace('_', ' ', $this->method));
    }
}

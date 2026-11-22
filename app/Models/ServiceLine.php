<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A billed hotel service on a reservation folio.
 * Name and price are snapshots taken when the line is added.
 */
class ServiceLine extends Model
{
    protected $table = 'reservation_service_lines';

    protected $fillable = [
        'reservation_id',
        'service_id',
        'name',
        'unit_price',
        'quantity',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }
}

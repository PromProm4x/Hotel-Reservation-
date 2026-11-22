<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use RuntimeException;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $reservations,
    ) {}

    /**
     * POST /api/v1/reservations (auth:sanctum)
     */
    public function store(StoreReservationRequest $request)
    {
        try {
            $reservation = $this->reservations->create([
                ...$request->validated(),
                'user_id' => $request->user()?->id,
                'source' => 'api',
            ]);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($this->serialize($reservation->fresh(['roomType', 'room', 'guest', 'serviceLines'])), 201);
    }

    /**
     * GET /api/v1/reservations/{code} (auth:sanctum)
     */
    public function show(Request $request, string $code)
    {
        $reservation = Reservation::query()
            ->where('code', $code)
            ->with(['roomType', 'room', 'guest', 'payments', 'serviceLines'])
            ->firstOrFail();

        $ownsBooking = $reservation->user_id === $request->user()->id
            || $reservation->guest?->user_id === $request->user()->id;

        abort_unless($ownsBooking || $request->user()->isStaff(), 403);

        return response()->json($this->serialize($reservation));
    }

    /**
     * @return array<string, mixed>
     */
    protected function serialize(Reservation $reservation): array
    {
        return [
            'code' => $reservation->code,
            'status' => $reservation->status->value,
            'payment_status' => $reservation->payment_status,
            'room_type' => $reservation->roomType?->name,
            'room_number' => $reservation->room?->room_number,
            'guest' => $reservation->guest?->full_name,
            'check_in' => $reservation->check_in->toDateString(),
            'check_out' => $reservation->check_out->toDateString(),
            'nights' => $reservation->nights,
            'adults' => $reservation->adults,
            'children' => $reservation->children,
            'subtotal' => (float) $reservation->subtotal,
            'services' => $reservation->serviceLines->map(fn ($line) => [
                'name' => $line->name,
                'quantity' => $line->quantity,
                'amount' => (float) $line->amount,
            ])->values(),
            'services_amount' => (float) $reservation->services_amount,
            'discount_amount' => (float) $reservation->discount_amount,
            'tax_amount' => (float) $reservation->tax_amount,
            'total_amount' => (float) $reservation->total_amount,
            'currency' => $reservation->currency,
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Reservation;

class PaymentController extends Controller
{
    /**
     * Record a payment (or refund) against a reservation.
     * Structured so a Stripe/PayPal gateway can later create these
     * records from webhooks using `method` + `reference`.
     */
    public function store(StorePaymentRequest $request, Reservation $reservation)
    {
        $data = $request->validated();

        // Payments recorded at the desk default to Completed.
        $data['status'] = $data['status'] ?? PaymentStatus::Completed->value;

        // Refunds are always stored with the Refunded status so they are
        // never counted as collected revenue or amount paid.
        if ($data['type'] === 'refund') {
            $data['status'] = PaymentStatus::Refunded->value;
        }

        $reservation->payments()->create([
            ...$data,
            'currency' => $reservation->currency,
            'recorded_by' => $request->user()->id,
            'paid_at' => $request->date('paid_at') ?? now(),
        ]);

        return back()->with('status', 'Payment recorded.');
    }

    public function destroy(Reservation $reservation, Payment $payment)
    {
        abort_unless($payment->reservation_id === $reservation->id, 404);

        $payment->delete(); // soft delete

        return back()->with('status', 'Payment removed.');
    }
}

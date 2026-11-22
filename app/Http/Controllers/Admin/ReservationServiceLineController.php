<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Service;
use App\Models\ServiceLine;
use App\Services\ReservationService;
use Illuminate\Http\Request;

class ReservationServiceLineController extends Controller
{
    public function __construct(
        protected ReservationService $reservations,
    ) {}

    /**
     * Add a chargeable hotel service to a reservation folio.
     */
    public function store(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $service = Service::query()->active()->findOrFail($data['service_id']);

        $this->reservations->addService($reservation, $service, (int) $data['quantity']);

        return back()->with('status', "{$service->name} added to the folio.");
    }

    public function destroy(Reservation $reservation, ServiceLine $line)
    {
        $this->reservations->removeService($reservation, $line);

        return back()->with('status', 'Service removed from the folio.');
    }
}

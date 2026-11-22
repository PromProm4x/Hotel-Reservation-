<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use App\Services\AvailabilityService;

class DashboardController extends Controller
{
    public function __construct(
        protected AvailabilityService $availability,
    ) {}

    public function index()
    {
        $today = now()->toDateString();

        $revenue = [
            'today' => $this->revenueBetween(now()->startOfDay(), now()->endOfDay()),
            'week' => $this->revenueBetween(now()->startOfWeek(), now()->endOfWeek()),
            'month' => $this->revenueBetween(now()->startOfMonth(), now()->endOfMonth()),
        ];

        $arrivals = Reservation::query()
            ->whereDate('check_in', $today)
            ->whereIn('status', [ReservationStatus::Pending->value, ReservationStatus::Confirmed->value])
            ->with(['guest', 'roomType', 'room'])
            ->orderBy('check_in')
            ->get();

        $departures = Reservation::query()
            ->whereDate('check_out', $today)
            ->where('status', ReservationStatus::CheckedIn->value)
            ->with(['guest', 'roomType', 'room'])
            ->orderBy('check_out')
            ->get();

        $upcoming = Reservation::query()
            ->whereBetween('check_in', [now()->addDay()->toDateString(), now()->addDays(7)->toDateString()])
            ->blocking()
            ->with(['guest', 'roomType'])
            ->orderBy('check_in')
            ->take(10)
            ->get();

        $stats = [
            'occupancy_today' => $this->availability->occupancyRate($today),
            'in_house' => Reservation::query()->where('status', ReservationStatus::CheckedIn->value)->count(),
            'pending' => Reservation::query()->where('status', ReservationStatus::Pending->value)->count(),
        ];

        return view('admin.dashboard', compact('revenue', 'arrivals', 'departures', 'upcoming', 'stats'));
    }

    protected function revenueBetween(\DateTimeInterface $from, \DateTimeInterface $to): float
    {
        $paid = Payment::query()
            ->where('status', PaymentStatus::Completed->value)
            ->whereBetween('paid_at', [$from, $to])
            ->sum('amount');

        $refunded = Payment::query()
            ->where('status', PaymentStatus::Refunded->value)
            ->whereBetween('paid_at', [$from, $to])
            ->sum('amount');

        return (float) $paid - (float) $refunded;
    }
}

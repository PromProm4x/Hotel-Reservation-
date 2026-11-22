<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateReservationRequest;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RuntimeException;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $reservations,
    ) {}

    public function index(Request $request)
    {
        $reservations = Reservation::query()
            ->with(['guest', 'roomType', 'room', 'payments'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where(function ($query) use ($term) {
                    $query->where('code', 'like', $term)
                        ->orWhereHas('guest', function ($g) use ($term) {
                            $g->where('first_name', 'like', $term)
                                ->orWhere('last_name', 'like', $term)
                                ->orWhere('email', 'like', $term);
                        });
                });
            })
            ->when($request->filled('date'), function ($q) use ($request) {
                $date = $request->string('date');
                $q->where('check_in', '<=', $date)->where('check_out', '>', $date);
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.reservations.index', [
            'reservations' => $reservations,
            'statuses' => ReservationStatus::cases(),
        ]);
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['guest', 'roomType', 'room', 'payments.recordedBy', 'user', 'serviceLines']);

        return view('admin.reservations.show', [
            'reservation' => $reservation,
            'services' => \App\Models\Service::query()->active()->orderBy('name')->get(),
        ]);
    }

    /**
     * Walk-in booking form (front desk).
     */
    public function create()
    {
        return view('admin.reservations.create', [
            'roomTypes' => RoomType::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreReservationRequest $request)
    {
        try {
            $reservation = $this->reservations->create([
                ...$request->validated(),
                'source' => 'walk_in',
                'status' => ReservationStatus::Confirmed,
            ]);
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['availability' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.reservations.show', $reservation)
            ->with('status', 'Walk-in booking created.');
    }

    /**
     * Edit stay details, room assignment, and pricing.
     */
    public function edit(Reservation $reservation)
    {
        $reservation->load(['guest', 'roomType', 'room']);

        return view('admin.reservations.edit', [
            'reservation' => $reservation,
            'roomTypes' => RoomType::query()->orderBy('name')->get(),
            'rooms' => Room::query()->with('roomType')->orderBy('room_type_id')->orderBy('room_number')->get(),
        ]);
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        try {
            $this->reservations->update($reservation, $request->validated());
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['availability' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.reservations.show', $reservation)
            ->with('status', 'Reservation updated.');
    }

    public function destroy(Reservation $reservation)
    {
        try {
            $this->reservations->delete($reservation);
        } catch (RuntimeException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.reservations.index')
            ->with('status', "Reservation {$reservation->code} deleted.");
    }

    /**
     * Status transitions: confirm, cancel, no-show.
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $action = $request->validate([
            'action' => ['required', 'in:confirm,cancel,no_show'],
        ])['action'];

        try {
            match ($action) {
                'confirm' => $this->reservations->confirm($reservation),
                'cancel' => $this->reservations->cancel($reservation),
                'no_show' => $this->reservations->markNoShow($reservation),
            };
        } catch (RuntimeException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('status', 'Reservation updated.');
    }

    /**
     * Monthly booking calendar.
     */
    public function calendar(Request $request)
    {
        // Accept only a valid "YYYY-MM" month; anything else falls back to now.
        $month = now();

        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $request->query('month'))) {
            $month = Carbon::createFromFormat('Y-m', $request->query('month'));
        }
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $reservations = Reservation::query()
            ->blocking()
            ->where('check_in', '<=', $end->toDateString())
            ->where('check_out', '>', $start->toDateString())
            ->with(['guest', 'roomType', 'room'])
            ->orderBy('check_in')
            ->get();

        // Group reservations by each date of the stay within the month.
        $byDate = [];
        foreach ($reservations as $reservation) {
            $cursor = $reservation->check_in->copy()->max($start);
            $last = $reservation->check_out->copy()->min($end->copy()->addDay());

            while ($cursor->lt($last)) {
                $byDate[$cursor->toDateString()][] = $reservation;
                $cursor->addDay();
            }
        }

        // The view calls collection methods on each day's entries.
        $byDate = array_map(fn ($items) => collect($items), $byDate);

        return view('admin.reservations.calendar', [
            'month' => $start,
            'byDate' => $byDate,
        ]);
    }
}

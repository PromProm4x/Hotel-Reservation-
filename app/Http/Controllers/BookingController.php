<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomSearchRequest;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\RoomType;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use RuntimeException;

class BookingController extends Controller
{
    public function __construct(
        protected ReservationService $reservations,
    ) {}

    /**
     * Booking form for a chosen room type + dates.
     */
    public function create(RoomSearchRequest $request, RoomType $roomType)
    {
        $criteria = $request->validated();
        $quote = $this->reservations->quote($roomType, $criteria['check_in'], $criteria['check_out']);

        return view('bookings.create', compact('roomType', 'criteria', 'quote'));
    }

    /**
     * Create the reservation.
     */
    public function store(StoreReservationRequest $request)
    {
        try {
            $reservation = $this->reservations->create([
                ...$request->validated(),
                'user_id' => $request->user()?->id,
                'source' => 'online',
            ]);
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['availability' => $e->getMessage()]);
        }

        return redirect()
            ->route('bookings.show', $reservation->code)
            ->with('status', 'Your reservation has been received. A confirmation email is on its way.');
    }

    /**
     * Booking confirmation / details (looked up by public code).
     */
    public function show(string $code)
    {
        $reservation = Reservation::query()
            ->where('code', $code)
            ->with(['roomType', 'room', 'guest', 'payments', 'serviceLines'])
            ->firstOrFail();

        return view('bookings.show', compact('reservation'));
    }

    /**
     * The signed-in guest's bookings.
     */
    public function index(Request $request)
    {
        $reservations = Reservation::query()
            ->where(function ($query) use ($request) {
                $query->where('user_id', $request->user()->id)
                    ->orWhereHas('guest', fn ($q) => $q->where('user_id', $request->user()->id));
            })
            ->with(['roomType', 'room'])
            ->orderByDesc('check_in')
            ->paginate(10);

        return view('bookings.index', compact('reservations'));
    }

    /**
     * Guest-initiated cancellation.
     */
    public function cancel(Request $request, string $code)
    {
        $reservation = Reservation::query()->where('code', $code)->firstOrFail();

        $ownsBooking = $reservation->user_id === $request->user()?->id
            || $reservation->guest?->user_id === $request->user()?->id;

        abort_unless($ownsBooking || ($request->user()?->isStaff() ?? false), 403);

        try {
            $this->reservations->cancel($reservation);
        } catch (RuntimeException $e) {
            return back()->withErrors(['cancel' => $e->getMessage()]);
        }

        return back()->with('status', 'Your reservation has been cancelled.');
    }
}

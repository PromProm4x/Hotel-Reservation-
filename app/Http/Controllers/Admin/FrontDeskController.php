<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class FrontDeskController extends Controller
{
    public function __construct(
        protected ReservationService $reservations,
    ) {}

    /**
     * Room status board (housekeeping view), grouped by floor.
     */
    public function board()
    {
        $roomsByFloor = Room::query()
            ->with(['roomType', 'currentReservation.guest'])
            ->orderBy('floor')
            ->orderBy('room_number')
            ->get()
            ->groupBy('floor');

        return view('admin.frontdesk.board', compact('roomsByFloor'));
    }

    public function checkIn(Reservation $reservation)
    {
        try {
            $this->reservations->checkIn($reservation);
        } catch (RuntimeException $e) {
            return back()->withErrors(['frontdesk' => $e->getMessage()]);
        }

        return back()->with('status', "Checked in {$reservation->guest->full_name} — room {$reservation->room?->room_number}.");
    }

    public function checkOut(Reservation $reservation)
    {
        try {
            $this->reservations->checkOut($reservation);
        } catch (RuntimeException $e) {
            return back()->withErrors(['frontdesk' => $e->getMessage()]);
        }

        return back()->with('status', "Checked out {$reservation->guest->full_name} — room {$reservation->room?->room_number} is now due for cleaning.");
    }

    /**
     * Housekeeping: update a single room's status.
     */
    public function updateRoomStatus(Request $request, Room $room)
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(RoomStatus::class)],
        ]);

        $room->update(['status' => $data['status']]);

        return back()->with('status', "Room {$room->room_number} marked as {$room->status->label()}.");
    }
}

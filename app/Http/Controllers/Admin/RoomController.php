<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomRequest;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::query()
            ->with('roomType')
            ->when($request->filled('room_type_id'), fn ($q) => $q->where('room_type_id', $request->integer('room_type_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderBy('floor')
            ->orderBy('room_number')
            ->paginate(25)
            ->withQueryString();

        $roomTypes = RoomType::query()->orderBy('name')->get();

        return view('admin.rooms.index', compact('rooms', 'roomTypes'));
    }

    public function create()
    {
        return view('admin.rooms.create', [
            'room' => new Room,
            'roomTypes' => RoomType::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreRoomRequest $request)
    {
        Room::query()->create($request->validated());

        return redirect()
            ->route('admin.rooms.index')
            ->with('status', 'Room created.');
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', [
            'room' => $room,
            'roomTypes' => RoomType::query()->orderBy('name')->get(),
        ]);
    }

    public function update(StoreRoomRequest $request, Room $room)
    {
        $room->update($request->validated());

        return redirect()
            ->route('admin.rooms.index')
            ->with('status', 'Room updated.');
    }

    public function destroy(Room $room)
    {
        $hasActiveStays = $room->reservations()
            ->whereIn('status', \App\Enums\ReservationStatus::blockingValues())
            ->where('check_out', '>=', now()->toDateString())
            ->exists();

        if ($hasActiveStays) {
            return back()->with('status', 'This room has upcoming or in-house reservations and cannot be deleted.');
        }

        $room->delete(); // soft delete

        return redirect()
            ->route('admin.rooms.index')
            ->with('status', 'Room deleted.');
    }
}

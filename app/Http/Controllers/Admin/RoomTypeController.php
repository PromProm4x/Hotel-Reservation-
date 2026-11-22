<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomTypeRequest;
use App\Models\RoomType;

class RoomTypeController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::query()
            ->withCount('rooms')
            ->orderBy('base_price')
            ->paginate(15);

        return view('admin.room-types.index', compact('roomTypes'));
    }

    public function create()
    {
        return view('admin.room-types.create', ['roomType' => new RoomType]);
    }

    public function store(StoreRoomTypeRequest $request)
    {
        RoomType::query()->create($request->payload());

        return redirect()
            ->route('admin.room-types.index')
            ->with('status', 'Room type created.');
    }

    public function edit(RoomType $roomType)
    {
        return view('admin.room-types.edit', compact('roomType'));
    }

    public function update(StoreRoomTypeRequest $request, RoomType $roomType)
    {
        $roomType->update($request->payload());

        return redirect()
            ->route('admin.room-types.index')
            ->with('status', 'Room type updated.');
    }

    public function destroy(RoomType $roomType)
    {
        if ($roomType->rooms()->withTrashed()->exists()) {
            return back()->with('status', 'This category still has rooms. Delete or reassign them first.');
        }

        $roomType->delete(); // soft delete

        return redirect()
            ->route('admin.room-types.index')
            ->with('status', 'Room type deleted.');
    }
}

@extends('layouts.admin')

@section('title', 'Rooms')

@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-2xl" style="font-family: 'Playfair Display', serif;">Rooms</h1>
    <a href="{{ route('admin.rooms.create') }}" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-4 py-2 text-sm hover:bg-[#75323d]">New room</a>
</div>

<form method="GET" class="mt-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Room type</label>
        <select name="room_type_id" class="rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
            <option value="">All</option>
            @foreach ($roomTypes as $type)
                <option value="{{ $type->id }}" @selected(request('room_type_id') == $type->id)>{{ $type->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Status</label>
        <select name="status" class="rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
            <option value="">All</option>
            @foreach (\App\Enums\RoomStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="rounded-lg border border-[#e9dcc2] bg-white px-4 py-2 text-sm">Filter</button>
</form>

<div class="bg-white rounded-xl border border-[#e9dcc2] mt-4 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="text-left text-xs uppercase tracking-wider text-[#a9853f]">
            <tr>
                <th class="px-5 py-3">Room</th>
                <th class="px-5 py-3">Floor</th>
                <th class="px-5 py-3">Type</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3">Notes</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#f6ecd9]">
            @forelse ($rooms as $room)
                <tr>
                    <td class="px-5 py-3 font-medium">{{ $room->room_number }}</td>
                    <td class="px-5 py-3">{{ $room->floor }}</td>
                    <td class="px-5 py-3">{{ $room->roomType->name }}</td>
                    <td class="px-5 py-3"><span class="text-xs px-2 py-1 rounded-full {{ $room->status->badgeClasses() }}">{{ $room->status->label() }}</span></td>
                    <td class="px-5 py-3 text-[#75323d]/60 max-w-[16rem] truncate">{{ $room->notes }}</td>
                    <td class="px-5 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('admin.rooms.edit', $room) }}" class="text-[#a9853f] hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" class="inline ml-3"
                              onsubmit="return confirm('Delete this room?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-700 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-6 text-[#75323d]/60">No rooms found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $rooms->links() }}</div>
@endsection

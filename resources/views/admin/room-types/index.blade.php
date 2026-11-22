@extends('layouts.admin')

@section('title', 'Room Types')

@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-2xl" style="font-family: 'Playfair Display', serif;">Room Types</h1>
    <a href="{{ route('admin.room-types.create') }}" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-4 py-2 text-sm hover:bg-[#75323d]">New room type</a>
</div>

<div class="bg-white rounded-xl border border-[#e9dcc2] mt-6 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="text-left text-xs uppercase tracking-wider text-[#a9853f]">
            <tr>
                <th class="px-5 py-3">Name</th>
                <th class="px-5 py-3 text-right">Base price</th>
                <th class="px-5 py-3">Capacity</th>
                <th class="px-5 py-3 text-center">Rooms</th>
                <th class="px-5 py-3">Active</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#f6ecd9]">
            @forelse ($roomTypes as $roomType)
                <tr>
                    <td class="px-5 py-3 font-medium">{{ $roomType->name }}</td>
                    <td class="px-5 py-3 text-right">${{ number_format($roomType->base_price, 2) }}</td>
                    <td class="px-5 py-3">{{ $roomType->capacity_adults }}A + {{ $roomType->capacity_children }}C</td>
                    <td class="px-5 py-3 text-center">{{ $roomType->rooms_count }}</td>
                    <td class="px-5 py-3">{{ $roomType->is_active ? 'Yes' : 'No' }}</td>
                    <td class="px-5 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('admin.room-types.edit', $roomType) }}" class="text-[#a9853f] hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.room-types.destroy', $roomType) }}" class="inline ml-3"
                              onsubmit="return confirm('Delete this room type?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-700 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-6 text-[#75323d]/60">No room types yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $roomTypes->links() }}</div>
@endsection

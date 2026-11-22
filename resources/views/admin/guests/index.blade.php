@extends('layouts.admin')

@section('title', 'Guests')

@section('content')
<h1 class="text-2xl" style="font-family: 'Playfair Display', serif;">Guests</h1>

<form method="GET" class="mt-6 flex gap-3 items-end">
    <div>
        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, phone…"
               class="rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm w-72">
    </div>
    <button type="submit" class="rounded-lg border border-[#e9dcc2] bg-white px-4 py-2 text-sm">Search</button>
</form>

<div class="bg-white rounded-xl border border-[#e9dcc2] mt-4 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="text-left text-xs uppercase tracking-wider text-[#a9853f]">
            <tr>
                <th class="px-5 py-3">Guest</th>
                <th class="px-5 py-3">Email</th>
                <th class="px-5 py-3">Phone</th>
                <th class="px-5 py-3 text-center">Stays</th>
                <th class="px-5 py-3">Last stay</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#f6ecd9]">
            @forelse ($guests as $guest)
                <tr class="hover:bg-[#fbf6ec]">
                    <td class="px-5 py-3"><a href="{{ route('admin.guests.show', $guest) }}" class="font-medium text-[#a9853f] hover:underline">{{ $guest->full_name }}</a></td>
                    <td class="px-5 py-3">{{ $guest->email }}</td>
                    <td class="px-5 py-3">{{ $guest->phone }}</td>
                    <td class="px-5 py-3 text-center">{{ $guest->reservations_count }}</td>
                    <td class="px-5 py-3">{{ $guest->reservations_max_check_in ? \Carbon\Carbon::parse($guest->reservations_max_check_in)->format('d M Y') : '—' }}</td>
                    <td class="px-5 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('admin.guests.edit', $guest) }}" class="text-[#a9853f] hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.guests.destroy', $guest) }}" class="inline ml-3"
                              onsubmit="return confirm('Delete this guest profile? Past bookings are kept.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-700 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-6 text-[#75323d]/60">No guests found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $guests->links() }}</div>
@endsection

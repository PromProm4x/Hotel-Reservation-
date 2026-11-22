@extends('layouts.admin')

@section('title', 'Reservations')

@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-2xl" style="font-family: 'Playfair Display', serif;">Reservations</h1>
    <a href="{{ route('admin.reservations.create') }}" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-4 py-2 text-sm hover:bg-[#75323d]">Walk-in booking</a>
</div>

<form method="GET" class="mt-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Code, guest name, email…"
               class="rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm w-64">
    </div>
    <div>
        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Status</label>
        <select name="status" class="rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
            <option value="">All</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">In-house on</label>
        <input type="date" name="date" value="{{ request('date') }}" class="rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
    </div>
    <button type="submit" class="rounded-lg border border-[#e9dcc2] bg-white px-4 py-2 text-sm">Filter</button>
</form>

<div class="bg-white rounded-xl border border-[#e9dcc2] mt-4 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="text-left text-xs uppercase tracking-wider text-[#a9853f]">
            <tr>
                <th class="px-5 py-3">Code</th>
                <th class="px-5 py-3">Guest</th>
                <th class="px-5 py-3">Room</th>
                <th class="px-5 py-3">Stay</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3">Payment</th>
                <th class="px-5 py-3 text-right">Total</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#f6ecd9]">
            @forelse ($reservations as $reservation)
                <tr class="hover:bg-[#fbf6ec]">
                    <td class="px-5 py-3"><a href="{{ route('admin.reservations.show', $reservation) }}" class="text-[#a9853f] hover:underline">{{ $reservation->code }}</a></td>
                    <td class="px-5 py-3">{{ $reservation->guest->full_name }}</td>
                    <td class="px-5 py-3">{{ $reservation->roomType->name }} @if($reservation->room)({{ $reservation->room->room_number }})@endif</td>
                    <td class="px-5 py-3 whitespace-nowrap">{{ $reservation->check_in->format('d M') }} – {{ $reservation->check_out->format('d M Y') }}</td>
                    <td class="px-5 py-3"><span class="text-xs px-2 py-1 rounded-full {{ $reservation->status->badgeClasses() }}">{{ $reservation->status->label() }}</span></td>
                    <td class="px-5 py-3">{{ $reservation->payment_status_label }}</td>
                    <td class="px-5 py-3 text-right whitespace-nowrap">{{ $reservation->currency }} {{ number_format($reservation->total_amount, 2) }}</td>
                    <td class="px-5 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('admin.reservations.edit', $reservation) }}" class="text-[#a9853f] hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.reservations.destroy', $reservation) }}" class="inline-block ml-2"
                              onsubmit="return confirm('Delete reservation {{ $reservation->code }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-700 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-5 py-6 text-[#75323d]/60">No reservations match your filters.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $reservations->links() }}</div>
@endsection

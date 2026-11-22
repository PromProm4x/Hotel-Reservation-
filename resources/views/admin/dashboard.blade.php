@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl" style="font-family: 'Playfair Display', serif;">Dashboard</h1>
<p class="text-sm text-[#75323d]/60 mt-1">{{ now()->format('l, d F Y') }}</p>

<div class="grid gap-4 sm:grid-cols-3 mt-6">
    <div class="bg-white rounded-xl border border-[#e9dcc2] p-5">
        <p class="text-xs uppercase tracking-widest text-[#a9853f]">Occupancy today</p>
        <p class="mt-2 text-3xl">{{ $stats['occupancy_today'] }}%</p>
    </div>
    <div class="bg-white rounded-xl border border-[#e9dcc2] p-5">
        <p class="text-xs uppercase tracking-widest text-[#a9853f]">Guests in-house</p>
        <p class="mt-2 text-3xl">{{ $stats['in_house'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-[#e9dcc2] p-5">
        <p class="text-xs uppercase tracking-widest text-[#a9853f]">Pending bookings</p>
        <p class="mt-2 text-3xl">{{ $stats['pending'] }}</p>
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-3 mt-4">
    <div class="bg-[#2e1013] text-[#e9dcc2] rounded-xl p-5">
        <p class="text-xs uppercase tracking-widest text-[#cfae76]">Revenue today</p>
        <p class="mt-2 text-2xl">${{ number_format($revenue['today'], 2) }}</p>
    </div>
    <div class="bg-[#2e1013] text-[#e9dcc2] rounded-xl p-5">
        <p class="text-xs uppercase tracking-widest text-[#cfae76]">Revenue this week</p>
        <p class="mt-2 text-2xl">${{ number_format($revenue['week'], 2) }}</p>
    </div>
    <div class="bg-[#2e1013] text-[#e9dcc2] rounded-xl p-5">
        <p class="text-xs uppercase tracking-widest text-[#cfae76]">Revenue this month</p>
        <p class="mt-2 text-2xl">${{ number_format($revenue['month'], 2) }}</p>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-2 mt-8">
    <section class="bg-white rounded-xl border border-[#e9dcc2]">
        <h2 class="px-5 py-4 border-b border-[#f6ecd9] text-sm font-medium">Today's arrivals ({{ $arrivals->count() }})</h2>
        <div class="divide-y divide-[#f6ecd9]">
            @forelse ($arrivals as $reservation)
                <div class="px-5 py-3 flex items-center justify-between gap-3 text-sm">
                    <div>
                        <a href="{{ route('admin.reservations.show', $reservation) }}" class="font-medium hover:underline">{{ $reservation->guest->full_name }}</a>
                        <p class="text-[#75323d]/60">{{ $reservation->roomType->name }} · Room {{ $reservation->room?->room_number ?? 'TBA' }} · {{ $reservation->nights }} night(s)</p>
                    </div>
                    <form method="POST" action="{{ route('admin.frontdesk.check-in', $reservation) }}">
                        @csrf
                        <button type="submit" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-3 py-1.5 text-xs hover:bg-[#75323d]">Check in</button>
                    </form>
                </div>
            @empty
                <p class="px-5 py-6 text-sm text-[#75323d]/60">No arrivals expected today.</p>
            @endforelse
        </div>
    </section>

    <section class="bg-white rounded-xl border border-[#e9dcc2]">
        <h2 class="px-5 py-4 border-b border-[#f6ecd9] text-sm font-medium">Today's departures ({{ $departures->count() }})</h2>
        <div class="divide-y divide-[#f6ecd9]">
            @forelse ($departures as $reservation)
                <div class="px-5 py-3 flex items-center justify-between gap-3 text-sm">
                    <div>
                        <a href="{{ route('admin.reservations.show', $reservation) }}" class="font-medium hover:underline">{{ $reservation->guest->full_name }}</a>
                        <p class="text-[#75323d]/60">Room {{ $reservation->room?->room_number }} · Balance: {{ $reservation->currency }} {{ number_format($reservation->balance_due, 2) }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.frontdesk.check-out', $reservation) }}">
                        @csrf
                        <button type="submit" class="rounded-lg bg-[#c6a15b] text-[#2e1013] px-3 py-1.5 text-xs hover:bg-[#cfae76]">Check out</button>
                    </form>
                </div>
            @empty
                <p class="px-5 py-6 text-sm text-[#75323d]/60">No departures expected today.</p>
            @endforelse
        </div>
    </section>
</div>

<section class="bg-white rounded-xl border border-[#e9dcc2] mt-6">
    <h2 class="px-5 py-4 border-b border-[#f6ecd9] text-sm font-medium">Upcoming arrivals (next 7 days)</h2>
    <table class="w-full text-sm">
        <thead class="text-left text-xs uppercase tracking-wider text-[#a9853f]">
            <tr>
                <th class="px-5 py-3">Date</th>
                <th class="px-5 py-3">Guest</th>
                <th class="px-5 py-3">Room type</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#f6ecd9]">
            @forelse ($upcoming as $reservation)
                <tr>
                    <td class="px-5 py-3">{{ $reservation->check_in->format('d M') }}</td>
                    <td class="px-5 py-3"><a href="{{ route('admin.reservations.show', $reservation) }}" class="hover:underline">{{ $reservation->guest->full_name }}</a></td>
                    <td class="px-5 py-3">{{ $reservation->roomType->name }}</td>
                    <td class="px-5 py-3"><span class="text-xs px-2 py-1 rounded-full {{ $reservation->status->badgeClasses() }}">{{ $reservation->status->label() }}</span></td>
                    <td class="px-5 py-3 text-right">{{ $reservation->currency }} {{ number_format($reservation->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-6 text-[#75323d]/60">Nothing on the horizon yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection

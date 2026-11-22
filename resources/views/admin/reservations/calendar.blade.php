@extends('layouts.admin')

@section('title', 'Booking Calendar')

@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-2xl" style="font-family: 'Playfair Display', serif;">Booking Calendar</h1>
    <div class="flex items-center gap-4 text-sm">
        <a href="{{ route('admin.reservations.calendar', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}" class="text-[#a9853f] hover:underline">← {{ $month->copy()->subMonth()->format('M Y') }}</a>
        <span class="font-medium">{{ $month->format('F Y') }}</span>
        <a href="{{ route('admin.reservations.calendar', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}" class="text-[#a9853f] hover:underline">{{ $month->copy()->addMonth()->format('M Y') }} →</a>
    </div>
</div>

@php
    $firstDow = $month->copy()->startOfMonth()->dayOfWeekIso; // 1 (Mon) .. 7 (Sun)
    $daysInMonth = $month->daysInMonth;
@endphp

<div class="bg-white rounded-xl border border-[#e9dcc2] mt-6 overflow-hidden">
    <div class="grid grid-cols-7 text-xs uppercase tracking-wider text-[#a9853f] border-b border-[#f6ecd9]">
        @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dow)
            <div class="px-2 py-2 text-center">{{ $dow }}</div>
        @endforeach
    </div>
    <div class="grid grid-cols-7">
        @for ($blank = 1; $blank < $firstDow; $blank++)
            <div class="min-h-24 border-b border-r border-[#f6ecd9] bg-[#fbf6ec]"></div>
        @endfor

        @for ($day = 1; $day <= $daysInMonth; $day++)
            @php
                $date = $month->copy()->day($day)->toDateString();
                $items = $byDate[$date] ?? collect();
                $isToday = $date === now()->toDateString();
            @endphp
            <div class="min-h-24 border-b border-r border-[#f6ecd9] p-1.5 {{ $isToday ? 'bg-[#f6ecd9]/70' : '' }}">
                <p class="text-xs {{ $isToday ? 'font-semibold text-[#a9853f]' : 'text-[#75323d]/60' }}">{{ $day }}</p>
                <div class="mt-1 space-y-1">
                    @foreach ($items->take(3) as $reservation)
                        <a href="{{ route('admin.reservations.show', $reservation) }}"
                           class="block truncate text-[11px] px-1.5 py-0.5 rounded {{ $reservation->status->badgeClasses() }}"
                           title="{{ $reservation->guest->full_name }} · {{ $reservation->roomType->name }}">
                            {{ $reservation->guest->last_name }} · {{ $reservation->room?->room_number ?? $reservation->roomType->name }}
                        </a>
                    @endforeach
                    @if ($items->count() > 3)
                        <p class="text-[10px] text-[#75323d]/60 px-1">+ {{ $items->count() - 3 }} more</p>
                    @endif
                </div>
            </div>
        @endfor
    </div>
</div>
<p class="mt-3 text-xs text-[#75323d]/60">Reservations appear on every night of the stay. Click an entry to open the reservation.</p>
@endsection

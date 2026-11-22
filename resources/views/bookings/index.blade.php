@extends('layouts.app')

@section('title', 'My Bookings — De La Renaissance')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <h1 class="text-3xl" style="font-family: 'Playfair Display', serif;">My Bookings</h1>

    @if ($reservations->isEmpty())
        <div class="mt-8 rounded-2xl border border-[#e9dcc2] bg-white p-10 text-center text-[#75323d]/70">
            You have no bookings yet.
            <a href="{{ route('rooms.search') }}" class="text-[#a9853f] hover:underline">Browse rooms &amp; suites →</a>
        </div>
    @else
        <div class="mt-8 space-y-4">
            @foreach ($reservations as $reservation)
                <a href="{{ route('bookings.show', $reservation->code) }}"
                   class="block bg-white rounded-2xl border border-[#e9dcc2] shadow-sm p-6 hover:shadow-md transition">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-sm text-[#a9853f]">{{ $reservation->code }}</p>
                            <p class="mt-1 font-medium">{{ $reservation->roomType->name }}</p>
                            <p class="mt-1 text-sm text-[#75323d]/70">
                                {{ $reservation->check_in->format('d M Y') }} – {{ $reservation->check_out->format('d M Y') }} · {{ $reservation->nights }} night(s)
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs px-3 py-1.5 rounded-full {{ $reservation->status->badgeClasses() }}">{{ $reservation->status->label() }}</span>
                            <p class="mt-2 text-sm">{{ $reservation->currency }} {{ number_format($reservation->total_amount, 2) }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $reservations->links() }}
        </div>
    @endif
</div>
@endsection

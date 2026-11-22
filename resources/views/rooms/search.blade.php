@extends('layouts.app')

@section('title', 'Rooms & Suites — De La Renaissance')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
    <div class="text-center">
        <p class="uppercase tracking-[0.3em] text-xs text-[#a9853f]">Find your stay</p>
        <h1 class="mt-2 text-3xl" style="font-family: 'Playfair Display', serif;">Rooms &amp; Suites</h1>
    </div>

    <form action="{{ route('rooms.search') }}" method="GET"
          class="mt-8 bg-white rounded-2xl border border-[#e9dcc2] shadow-sm p-6 grid gap-4 sm:grid-cols-5 items-end">
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1" for="check_in">Check-in</label>
            <input type="date" id="check_in" name="check_in" required min="{{ now()->toDateString() }}"
                   value="{{ $criteria['check_in'] ?? old('check_in') }}"
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1" for="check_out">Check-out</label>
            <input type="date" id="check_out" name="check_out" required min="{{ now()->addDay()->toDateString() }}"
                   value="{{ $criteria['check_out'] ?? old('check_out') }}"
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1" for="guests">Guests</label>
            <select id="guests" name="guests" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                @foreach (range(1, 6) as $n)
                    <option value="{{ $n }}" @selected(($criteria['guests'] ?? 2) == $n)>{{ $n }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1" for="room_type_id">Room type</label>
            <select id="room_type_id" name="room_type_id" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                <option value="">Any</option>
                @foreach ($roomTypes as $type)
                    <option value="{{ $type->id }}" @selected(($criteria['room_type_id'] ?? null) == $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-6 py-2.5 text-sm hover:bg-[#75323d] transition">
            Search
        </button>
    </form>

    @if (! is_null($results))
        <div class="mt-10">
            <h2 class="text-xl" style="font-family: 'Playfair Display', serif;">
                Available for {{ \Carbon\Carbon::parse($criteria['check_in'])->format('d M Y') }} – {{ \Carbon\Carbon::parse($criteria['check_out'])->format('d M Y') }}
            </h2>

            @if ($results->isEmpty())
                <div class="mt-6 rounded-2xl border border-[#e9dcc2] bg-white p-10 text-center text-[#75323d]/70">
                    No rooms match your dates. Please try different dates or a smaller party.
                </div>
            @else
                <div class="mt-6 space-y-5">
                    @foreach ($results as $roomType)
                        <article class="bg-white rounded-2xl border border-[#e9dcc2] shadow-sm p-6 flex flex-col sm:flex-row gap-6">
                            <div class="sm:w-44 h-32 shrink-0 rounded-xl bg-gradient-to-br from-[#75323d] to-[#2e1013] flex items-center justify-center overflow-hidden">
                                @if ($roomType->cover_photo)
                                    <img src="{{ $roomType->cover_photo }}" alt="{{ $roomType->name }}" class="h-full w-full object-cover">
                                @else
                                    <span class="text-[#cfae76] text-center text-lg px-2" style="font-family: 'Playfair Display', serif;">{{ $roomType->name }}</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-baseline justify-between gap-4">
                                    <h3 class="text-lg" style="font-family: 'Playfair Display', serif;">{{ $roomType->name }}</h3>
                                    <p class="text-sm text-emerald-700">{{ $roomType->available_rooms_count }} left</p>
                                </div>
                                <p class="mt-1 text-sm text-[#75323d]/75 line-clamp-2">{{ $roomType->description }}</p>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    @foreach (array_slice($roomType->amenities ?? [], 0, 5) as $amenity)
                                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-[#f6ecd9] text-[#75323d]">{{ $amenity }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="sm:w-52 shrink-0 sm:border-l sm:border-[#f6ecd9] sm:pl-6 flex sm:flex-col items-center sm:items-end justify-between gap-3">
                                <div class="text-right">
                                    <p class="text-2xl text-[#5a1f28]">{{ $roomType->quote['currency'] }} {{ number_format($roomType->quote['total'], 2) }}</p>
                                    <p class="text-xs text-[#75323d]/60">{{ $roomType->quote['nights'] }} night(s) incl. {{ $roomType->quote['tax_rate'] }}% tax</p>
                                </div>
                                <a href="{{ route('bookings.create', array_merge(['roomType' => $roomType->slug], collect($criteria)->only(['check_in', 'check_out', 'guests'])->all())) }}"
                                   class="rounded-lg bg-[#c6a15b] text-[#2e1013] px-6 py-2.5 text-sm font-medium hover:bg-[#cfae76] transition">
                                    Book now
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 mt-10">
            @foreach ($roomTypes as $roomType)
                <article class="bg-white rounded-2xl border border-[#e9dcc2] shadow-sm p-5">
                    <div class="flex items-baseline justify-between">
                        <h3 class="text-lg" style="font-family: 'Playfair Display', serif;">{{ $roomType->name }}</h3>
                        <p class="text-sm text-[#a9853f]">from ${{ number_format($roomType->base_price, 0) }}</p>
                    </div>
                    <p class="mt-2 text-sm text-[#75323d]/75 line-clamp-3">{{ $roomType->description }}</p>
                    <a href="{{ route('rooms.show', $roomType) }}" class="mt-3 inline-block text-sm text-[#a9853f] hover:underline">Details →</a>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection

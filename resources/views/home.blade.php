@extends('layouts.app')

@section('content')
<section class="relative bg-[#2e1013] text-[#f6ecd9] overflow-hidden">
    <img src="/images/hotel/exterior-day.jpg" alt="De La Renaissance Hotel" class="theme-light-only absolute inset-0 h-full w-full object-cover">
    <img src="/images/hotel/exterior-night.jpg" alt="De La Renaissance Hotel at night" class="theme-dark-only absolute inset-0 h-full w-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-b from-[#2e1013]/75 via-[#2e1013]/30 to-[#2e1013]/90"></div>
    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 pt-24 pb-32 text-center">
        <p class="uppercase tracking-[0.35em] text-xs text-[#cfae76]">Hotel &amp; Residences</p>
        <h1 class="mt-5 text-4xl sm:text-6xl" style="font-family: 'Playfair Display', serif;">De La Renaissance</h1>
        <p class="mt-5 max-w-xl mx-auto text-[#e9dcc2] font-light">
            Timeless hospitality in the heart of the city — where classic elegance meets modern comfort.
        </p>
    </div>
</section>

<section class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 -mt-16">
    <form action="{{ route('rooms.search') }}" method="GET"
          class="bg-white rounded-2xl shadow-xl border border-[#e9dcc2] p-6 grid gap-4 sm:grid-cols-4 items-end">
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1" for="check_in">Check-in</label>
            <input type="date" id="check_in" name="check_in" required min="{{ now()->toDateString() }}"
                   value="{{ old('check_in') }}"
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c6a15b]">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1" for="check_out">Check-out</label>
            <input type="date" id="check_out" name="check_out" required min="{{ now()->addDay()->toDateString() }}"
                   value="{{ old('check_out') }}"
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#c6a15b]">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1" for="guests">Guests</label>
            <select id="guests" name="guests" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                @foreach (range(1, 6) as $n)
                    <option value="{{ $n }}">{{ $n }} {{ \Illuminate\Support\Str::plural('guest', $n) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-6 py-2.5 text-sm hover:bg-[#75323d] transition">
            Check availability
        </button>
    </form>
</section>

<section class="max-w-6xl mx-auto px-4 sm:px-6 mt-20">
    <div class="text-center">
        <p class="uppercase tracking-[0.3em] text-xs text-[#a9853f]">Stay with us</p>
        <h2 class="mt-2 text-3xl" style="font-family: 'Playfair Display', serif;">Rooms &amp; Suites</h2>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 mt-10">
        @foreach ($roomTypes as $roomType)
            <article class="bg-white rounded-2xl border border-[#e9dcc2] overflow-hidden shadow-sm hover:shadow-md transition flex flex-col">
                <div class="h-44 bg-gradient-to-br from-[#75323d] to-[#2e1013] flex items-center justify-center">
                    @if ($roomType->cover_photo)
                        <img src="{{ $roomType->cover_photo }}" alt="{{ $roomType->name }}" class="h-full w-full object-cover">
                    @else
                        <span class="text-[#cfae76] text-2xl" style="font-family: 'Playfair Display', serif;">{{ $roomType->name }}</span>
                    @endif
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex items-baseline justify-between">
                        <h3 class="text-lg" style="font-family: 'Playfair Display', serif;">{{ $roomType->name }}</h3>
                        <p class="text-sm text-[#a9853f]">from <span class="text-lg font-medium">${{ number_format($roomType->base_price, 0) }}</span>/night</p>
                    </div>
                    <p class="mt-2 text-sm text-[#75323d]/80 line-clamp-3">{{ $roomType->description }}</p>
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @foreach (array_slice($roomType->amenities ?? [], 0, 4) as $amenity)
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-[#f6ecd9] text-[#75323d]">{{ $amenity }}</span>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t border-[#f6ecd9] flex items-center justify-between text-sm">
                        <span class="text-[#75323d]/70">Sleeps {{ $roomType->capacity_total }} · {{ $roomType->size_sqm }} m²</span>
                        <a href="{{ route('rooms.show', $roomType) }}" class="text-[#a9853f] hover:underline">Details →</a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
@endsection

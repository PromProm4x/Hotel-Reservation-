@extends('layouts.app')

@section('title', $roomType->name.' — De La Renaissance')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <a href="{{ route('rooms.search') }}" class="text-sm text-[#a9853f] hover:underline">← All rooms</a>

    <div class="mt-4 bg-white rounded-2xl border border-[#e9dcc2] shadow-sm overflow-hidden">
        <div class="h-64 bg-gradient-to-br from-[#75323d] to-[#2e1013] flex items-center justify-center">
            @if ($roomType->cover_photo)
                <img src="{{ $roomType->cover_photo }}" alt="{{ $roomType->name }}" class="h-full w-full object-cover">
            @else
                <span class="text-[#cfae76] text-3xl" style="font-family: 'Playfair Display', serif;">{{ $roomType->name }}</span>
            @endif
        </div>
        <div class="p-8">
            <div class="flex flex-wrap items-baseline justify-between gap-3">
                <h1 class="text-3xl" style="font-family: 'Playfair Display', serif;">{{ $roomType->name }}</h1>
                <p class="text-[#a9853f]">from <span class="text-2xl">${{ number_format($roomType->base_price, 0) }}</span> / night</p>
            </div>
            <p class="mt-2 text-sm text-[#75323d]/70">Sleeps {{ $roomType->capacity_adults }} adult(s)@if($roomType->capacity_children) + {{ $roomType->capacity_children }} child(ren)@endif · {{ $roomType->size_sqm }} m²</p>

            <p class="mt-5 text-[#75323d]/85 leading-relaxed">{{ $roomType->description }}</p>

            <h2 class="mt-8 text-xs uppercase tracking-widest text-[#a9853f]">Amenities</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                @forelse ($roomType->amenities ?? [] as $amenity)
                    <span class="text-xs px-3 py-1 rounded-full bg-[#f6ecd9] text-[#75323d]">{{ $amenity }}</span>
                @empty
                    <span class="text-sm text-[#75323d]/60">Details available on request.</span>
                @endforelse
            </div>

            <div class="mt-8 pt-6 border-t border-[#f6ecd9]">
                <form action="{{ route('rooms.search') }}" method="GET" class="grid gap-4 sm:grid-cols-4 items-end">
                    <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Check-in</label>
                        <input type="date" name="check_in" required min="{{ now()->toDateString() }}" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Check-out</label>
                        <input type="date" name="check_out" required min="{{ now()->addDay()->toDateString() }}" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Guests</label>
                        <select name="guests" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                            @foreach (range(1, $roomType->capacity_total) as $n)
                                <option value="{{ $n }}">{{ $n }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-6 py-2.5 text-sm hover:bg-[#75323d] transition">Check availability</button>
                </form>
            </div>
        </div>
    </div>

    @if ($reviews->isNotEmpty())
        <h2 class="mt-10 text-xl" style="font-family: 'Playfair Display', serif;">Guest impressions</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            @foreach ($reviews as $review)
                <div class="bg-white rounded-xl border border-[#e9dcc2] p-5">
                    <p class="text-[#c6a15b] text-sm">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</p>
                    <p class="mt-2 text-sm text-[#75323d]/85">{{ $review->comment }}</p>
                    <p class="mt-3 text-xs text-[#75323d]/60">— {{ $review->guest?->first_name }} {{ \Illuminate\Support\Str::substr($review->guest?->last_name ?? '', 0, 1) }}.</p>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

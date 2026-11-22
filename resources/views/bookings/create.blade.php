@extends('layouts.app')

@section('title', 'Complete your booking — De La Renaissance')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
    <h1 class="text-3xl" style="font-family: 'Playfair Display', serif;">Complete your booking</h1>

    <div class="mt-8 grid gap-8 lg:grid-cols-5">
        <form method="POST" action="{{ route('bookings.store') }}" class="lg:col-span-3 bg-white rounded-2xl border border-[#e9dcc2] shadow-sm p-8 space-y-5">
            @csrf
            <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
            <input type="hidden" name="check_in" value="{{ $criteria['check_in'] }}">
            <input type="hidden" name="check_out" value="{{ $criteria['check_out'] }}">

            <h2 class="text-xs uppercase tracking-widest text-[#a9853f]">Guest details</h2>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="first_name" class="block text-sm mb-1">First name</label>
                    <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required
                           class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="last_name" class="block text-sm mb-1">Last name</label>
                    <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="email" class="block text-sm mb-1">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required
                           class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                </div>
                <div>
                    <label for="phone" class="block text-sm mb-1">Phone (optional)</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="adults" class="block text-sm mb-1">Adults</label>
                    <select id="adults" name="adults" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                        @foreach (range(1, $roomType->capacity_adults) as $n)
                            <option value="{{ $n }}" @selected(old('adults', min($criteria['guests'], $roomType->capacity_adults)) == $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="children" class="block text-sm mb-1">Children</label>
                    <select id="children" name="children" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                        @foreach (range(0, $roomType->capacity_children) as $n)
                            <option value="{{ $n }}" @selected(old('children', 0) == $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label for="special_requests" class="block text-sm mb-1">Special requests (optional)</label>
                <textarea id="special_requests" name="special_requests" rows="3"
                          placeholder="Allergies, arrival time, preferred floor…"
                          class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">{{ old('special_requests') }}</textarea>
            </div>

            <button type="submit" class="w-full rounded-lg bg-[#c6a15b] text-[#2e1013] py-3 text-sm font-medium hover:bg-[#cfae76] transition">
                Confirm reservation
            </button>
            <p class="text-xs text-center text-[#75323d]/60">No payment is taken online — settle at the hotel or on arrival.</p>
        </form>

        <aside class="lg:col-span-2">
            <div class="bg-[#2e1013] text-[#e9dcc2] rounded-2xl p-8 sticky top-24">
                <h2 class="text-lg" style="font-family: 'Playfair Display', serif;">{{ $roomType->name }}</h2>
                <dl class="mt-5 space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-[#cfae76]">Check-in</dt><dd>{{ \Carbon\Carbon::parse($criteria['check_in'])->format('D, d M Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-[#cfae76]">Check-out</dt><dd>{{ \Carbon\Carbon::parse($criteria['check_out'])->format('D, d M Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-[#cfae76]">Nights</dt><dd>{{ $quote['nights'] }}</dd></div>
                    <div class="flex justify-between"><dt class="text-[#cfae76]">Rate / night</dt><dd>{{ $quote['currency'] }} {{ number_format($quote['nightly_rate'], 2) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-[#cfae76]">Subtotal</dt><dd>{{ $quote['currency'] }} {{ number_format($quote['subtotal'], 2) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-[#cfae76]">Tax ({{ $quote['tax_rate'] }}%)</dt><dd>{{ $quote['currency'] }} {{ number_format($quote['tax_amount'], 2) }}</dd></div>
                </dl>
                <div class="mt-5 pt-5 border-t border-[#75323d] flex justify-between items-baseline">
                    <span class="text-[#cfae76] text-sm">Total</span>
                    <span class="text-2xl">{{ $quote['currency'] }} {{ number_format($quote['total'], 2) }}</span>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

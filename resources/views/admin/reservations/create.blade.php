@extends('layouts.admin')

@section('title', 'Walk-in Booking')

@section('content')
<h1 class="text-2xl" style="font-family: 'Playfair Display', serif;">Walk-in Booking</h1>
<p class="text-sm text-[#75323d]/60 mt-1">Create a reservation on behalf of a guest at the front desk.</p>

<form method="POST" action="{{ route('admin.reservations.store') }}" class="bg-white rounded-xl border border-[#e9dcc2] p-6 mt-6 max-w-2xl space-y-5">
    @csrf
    <input type="hidden" name="source" value="walk_in">

    <h2 class="text-xs uppercase tracking-widest text-[#a9853f]">Stay</h2>
    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <label class="block text-sm mb-1" for="room_type_id">Room type</label>
            <select id="room_type_id" name="room_type_id" required class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                @foreach ($roomTypes as $type)
                    <option value="{{ $type->id }}" @selected(old('room_type_id') == $type->id)>{{ $type->name }} (${{ number_format($type->base_price, 0) }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1" for="check_in">Check-in</label>
            <input id="check_in" type="date" name="check_in" value="{{ old('check_in', now()->toDateString()) }}" required class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm mb-1" for="check_out">Check-out</label>
            <input id="check_out" type="date" name="check_out" value="{{ old('check_out', now()->addDay()->toDateString()) }}" required class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm mb-1" for="adults">Adults</label>
            <input id="adults" type="number" min="1" max="10" name="adults" value="{{ old('adults', 1) }}" required class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm mb-1" for="children">Children</label>
            <input id="children" type="number" min="0" max="10" name="children" value="{{ old('children', 0) }}" required class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
    </div>

    <h2 class="text-xs uppercase tracking-widest text-[#a9853f] pt-2">Guest</h2>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm mb-1" for="first_name">First name</label>
            <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm mb-1" for="last_name">Last name</label>
            <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm mb-1" for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm mb-1" for="phone">Phone (optional)</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
    </div>
    <div>
        <label class="block text-sm mb-1" for="special_requests">Special requests (optional)</label>
        <textarea id="special_requests" name="special_requests" rows="2" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">{{ old('special_requests') }}</textarea>
    </div>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="check_in_now" value="1" @checked(old('check_in_now')) class="rounded border-[#e9dcc2]">
        Check the guest in immediately
    </label>

    <div class="flex gap-3 pt-2">
        <button type="submit" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-5 py-2 text-sm hover:bg-[#75323d]">Create booking</button>
        <a href="{{ route('admin.reservations.index') }}" class="rounded-lg border border-[#e9dcc2] px-5 py-2 text-sm">Cancel</a>
    </div>
</form>
@endsection

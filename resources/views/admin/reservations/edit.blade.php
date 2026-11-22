@extends('layouts.admin')

@section('title', 'Edit '.$reservation->code)

@section('content')
<div>
    <a href="{{ route('admin.reservations.show', $reservation) }}" class="text-sm text-[#a9853f] hover:underline">&larr; {{ $reservation->code }}</a>
    <h1 class="text-2xl mt-1" style="font-family: 'Playfair Display', serif;">Edit reservation</h1>
    <p class="text-sm text-[#75323d]/60 mt-1">{{ $reservation->guest->full_name }} &middot; booked {{ $reservation->created_at->format('d M Y') }}</p>
</div>

<form method="POST" action="{{ route('admin.reservations.update', $reservation) }}" class="mt-6 max-w-3xl bg-white rounded-xl border border-[#e9dcc2] p-6">
    @csrf
    @method('PUT')

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Check-in</label>
            <input type="date" name="check_in" value="{{ old('check_in', $reservation->check_in->toDateString()) }}" required
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Check-out</label>
            <input type="date" name="check_out" value="{{ old('check_out', $reservation->check_out->toDateString()) }}" required
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Adults</label>
            <input type="number" name="adults" min="1" max="10" value="{{ old('adults', $reservation->adults) }}" required
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Children</label>
            <input type="number" name="children" min="0" max="10" value="{{ old('children', $reservation->children) }}"
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Room type</label>
            <select name="room_type_id" id="room-type-select" required class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                @foreach ($roomTypes as $roomType)
                    <option value="{{ $roomType->id }}" @selected((int) old('room_type_id', $reservation->room_type_id) === $roomType->id)>
                        {{ $roomType->name }} &mdash; from {{ number_format($roomType->base_price, 2) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Room</label>
            <select name="room_id" id="room-select" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                <option value="">Auto-assign best available</option>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}" data-type="{{ $room->room_type_id }}" @selected((int) old('room_id', $reservation->room_id) === $room->id)>
                        Room {{ $room->room_number }} &mdash; {{ $room->roomType->name }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-[#75323d]/60">Availability is re-checked when you save.</p>
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Nightly rate ({{ $reservation->currency }})</label>
            <input type="number" step="0.01" min="0" name="nightly_rate" value="{{ old('nightly_rate', $reservation->nightly_rate) }}" required
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Discount ({{ $reservation->currency }})</label>
            <input type="number" step="0.01" min="0" name="discount_amount" value="{{ old('discount_amount', $reservation->discount_amount) }}"
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Special requests</label>
            <textarea name="special_requests" rows="3" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">{{ old('special_requests', $reservation->special_requests) }}</textarea>
        </div>
    </div>

    <div class="mt-5 rounded-lg bg-[#f6ecd9] p-4 text-sm text-[#75323d]/80">
        The folio is repriced automatically when you save:
        <span class="whitespace-nowrap">nightly rate &times; nights + services &minus; discount + tax ({{ \App\Models\HotelSetting::taxRate() }}%).</span>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-5 py-2.5 text-sm hover:bg-[#75323d]">Save changes</button>
        <a href="{{ route('admin.reservations.show', $reservation) }}" class="rounded-lg border border-[#e9dcc2] px-5 py-2.5 text-sm hover:bg-[#f6ecd9]">Cancel</a>
    </div>
</form>

<script>
    (function () {
        var typeSelect = document.getElementById('room-type-select');
        var roomSelect = document.getElementById('room-select');
        if (! typeSelect || ! roomSelect) { return; }

        function filterRooms() {
            var type = typeSelect.value;
            Array.prototype.forEach.call(roomSelect.options, function (option) {
                if (! option.value) { return; }
                var match = option.dataset.type === type;
                option.hidden = ! match;
                if (! match && option.selected) { roomSelect.value = ''; }
            });
        }

        typeSelect.addEventListener('change', filterRooms);
        filterRooms();
    })();
</script>
@endsection

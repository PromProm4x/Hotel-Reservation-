@extends('layouts.admin')

@section('title', $guest->full_name)

@section('content')
<div class="flex flex-wrap items-start justify-between gap-3">
    <div>
        <a href="{{ route('admin.guests.index') }}" class="text-sm text-[#a9853f] hover:underline">← Guests</a>
        <h1 class="text-2xl mt-1" style="font-family: 'Playfair Display', serif;">{{ $guest->full_name }}</h1>
        <p class="text-sm text-[#75323d]/60 mt-1">{{ $guest->email }}@if($guest->phone) · {{ $guest->phone }}@endif @if($guest->nationality) · {{ $guest->nationality }}@endif</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.guests.edit', $guest) }}" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-4 py-2 text-sm hover:bg-[#75323d]">Edit profile</a>
        <form method="POST" action="{{ route('admin.guests.destroy', $guest) }}"
              onsubmit="return confirm('Delete this guest profile? Past bookings are kept.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-lg border border-rose-200 text-rose-700 px-4 py-2 text-sm hover:bg-rose-50">Delete</button>
        </form>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-3 mt-6">
    <section class="bg-white rounded-xl border border-[#e9dcc2] p-6">
        <h2 class="text-xs uppercase tracking-widest text-[#a9853f]">Notes &amp; preferences</h2>
        <form method="POST" action="{{ route('admin.guests.update', $guest) }}" class="mt-4 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm mb-1" for="notes">Internal notes</label>
                <textarea id="notes" name="notes" rows="4" placeholder="Allergies, VIP, incidents…"
                          class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">{{ old('notes', $guest->notes) }}</textarea>
            </div>
            <div>
                <label class="block text-sm mb-1" for="preferences">Preferences <span class="text-[#75323d]/50">(comma-separated)</span></label>
                <input id="preferences" type="text" name="preferences" value="{{ old('preferences', implode(', ', $guest->preferences ?? [])) }}"
                       placeholder="High floor, extra pillows, late check-out"
                       class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
            </div>
            <button type="submit" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-4 py-2 text-sm hover:bg-[#75323d]">Save</button>
        </form>

        @if (! empty($guest->preferences))
            <div class="mt-4 flex flex-wrap gap-1.5">
                @foreach ($guest->preferences as $preference)
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-[#f6ecd9] text-[#75323d]">{{ $preference }}</span>
                @endforeach
            </div>
        @endif
    </section>

    <section class="lg:col-span-2 bg-white rounded-xl border border-[#e9dcc2]">
        <h2 class="px-5 py-4 border-b border-[#f6ecd9] text-sm font-medium">Booking history ({{ $guest->reservations->count() }})</h2>
        <table class="w-full text-sm">
            <thead class="text-left text-xs uppercase tracking-wider text-[#a9853f]">
                <tr>
                    <th class="px-5 py-3">Code</th>
                    <th class="px-5 py-3">Stay</th>
                    <th class="px-5 py-3">Room</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f6ecd9]">
                @forelse ($guest->reservations as $reservation)
                    <tr class="hover:bg-[#fbf6ec]">
                        <td class="px-5 py-3"><a href="{{ route('admin.reservations.show', $reservation) }}" class="text-[#a9853f] hover:underline">{{ $reservation->code }}</a></td>
                        <td class="px-5 py-3 whitespace-nowrap">{{ $reservation->check_in->format('d M') }} – {{ $reservation->check_out->format('d M Y') }}</td>
                        <td class="px-5 py-3">{{ $reservation->roomType->name }}@if($reservation->room) ({{ $reservation->room->room_number }})@endif</td>
                        <td class="px-5 py-3"><span class="text-xs px-2 py-1 rounded-full {{ $reservation->status->badgeClasses() }}">{{ $reservation->status->label() }}</span></td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">{{ $reservation->currency }} {{ number_format($reservation->total_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-6 text-[#75323d]/60">No bookings yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>
@endsection

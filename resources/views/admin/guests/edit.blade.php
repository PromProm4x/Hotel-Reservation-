@extends('layouts.admin')

@section('title', 'Edit '.$guest->full_name)

@section('content')
<a href="{{ route('admin.guests.show', $guest) }}" class="text-sm text-[#a9853f] hover:underline">← {{ $guest->full_name }}</a>
<h1 class="text-2xl mt-1" style="font-family: 'Playfair Display', serif;">Edit Guest Profile</h1>

<form method="POST" action="{{ route('admin.guests.update', $guest) }}" class="bg-white rounded-xl border border-[#e9dcc2] p-6 mt-6 max-w-2xl">
    @csrf
    @method('PUT')

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm mb-1" for="first_name">First name</label>
            <input id="first_name" type="text" name="first_name" value="{{ old('first_name', $guest->first_name) }}" required
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm mb-1" for="last_name">Last name</label>
            <input id="last_name" type="text" name="last_name" value="{{ old('last_name', $guest->last_name) }}" required
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm mb-1" for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $guest->email) }}" required
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm mb-1" for="phone">Phone</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone', $guest->phone) }}"
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm mb-1" for="nationality">Nationality</label>
            <input id="nationality" type="text" name="nationality" value="{{ old('nationality', $guest->nationality) }}"
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm mb-1" for="preferences">Preferences <span class="text-[#75323d]/50">(comma-separated)</span></label>
            <input id="preferences" type="text" name="preferences" value="{{ old('preferences', implode(', ', $guest->preferences ?? [])) }}"
                   placeholder="High floor, extra pillows, late check-out"
                   class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm mb-1" for="notes">Internal notes</label>
            <textarea id="notes" name="notes" rows="4" placeholder="Allergies, VIP, incidents…"
                      class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">{{ old('notes', $guest->notes) }}</textarea>
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <button type="submit" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-5 py-2 text-sm hover:bg-[#75323d]">Save changes</button>
        <a href="{{ route('admin.guests.show', $guest) }}" class="rounded-lg border border-[#e9dcc2] px-5 py-2 text-sm">Cancel</a>
    </div>
</form>
@endsection

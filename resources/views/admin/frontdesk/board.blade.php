@extends('layouts.admin')

@section('title', 'Room Board')

@section('content')
<h1 class="text-2xl" style="font-family: 'Playfair Display', serif;">Room Status Board</h1>
<p class="text-sm text-[#75323d]/60 mt-1">Live housekeeping and occupancy overview.</p>

<div class="mt-4 flex flex-wrap gap-4 text-xs">
    @foreach (\App\Enums\RoomStatus::cases() as $status)
        <span class="inline-flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full {{ $status->dotClasses() }}"></span> {{ $status->label() }}
        </span>
    @endforeach
</div>

@foreach ($roomsByFloor as $floor => $rooms)
    <section class="mt-8">
        <h2 class="text-xs uppercase tracking-widest text-[#a9853f]">Floor {{ $floor }}</h2>
        <div class="grid gap-3 mt-3" style="grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));">
            @foreach ($rooms as $room)
                <div class="bg-white rounded-xl border border-[#e9dcc2] p-4">
                    <div class="flex items-center justify-between">
                        <p class="font-medium">{{ $room->room_number }}</p>
                        <span class="w-3 h-3 rounded-full {{ $room->status->dotClasses() }}" title="{{ $room->status->label() }}"></span>
                    </div>
                    <p class="text-xs text-[#75323d]/60 mt-0.5">{{ $room->roomType->name }}</p>

                    @if ($room->currentReservation)
                        <a href="{{ route('admin.reservations.show', $room->currentReservation) }}" class="mt-2 block text-xs text-[#a9853f] hover:underline truncate">
                            {{ $room->currentReservation->guest->full_name }}
                        </a>
                        <p class="text-[11px] text-[#75323d]/60">until {{ $room->currentReservation->check_out->format('d M') }}</p>
                        @if ($room->currentReservation->canCheckOut())
                            <form method="POST" action="{{ route('admin.frontdesk.check-out', $room->currentReservation) }}" class="mt-2">
                                @csrf
                                <button type="submit" class="w-full rounded-lg bg-[#c6a15b] text-[#2e1013] py-1.5 text-xs hover:bg-[#cfae76]">Check out</button>
                            </form>
                        @endif
                    @else
                        <form method="POST" action="{{ route('admin.frontdesk.room-status', $room) }}" class="mt-2">
                            @csrf
                            <select name="status" onchange="this.form.submit()" class="w-full rounded-lg border border-[#e9dcc2] px-2 py-1.5 text-xs">
                                @foreach (\App\Enums\RoomStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected($room->status === $status)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
@endforeach
@endsection

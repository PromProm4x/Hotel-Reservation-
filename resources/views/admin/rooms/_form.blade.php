<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm mb-1" for="room_number">Room number</label>
        <input id="room_number" type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" required
               class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm mb-1" for="floor">Floor</label>
        <input id="floor" type="number" min="0" name="floor" value="{{ old('floor', $room->floor ?? 1) }}" required
               class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm mb-1" for="room_type_id">Room type</label>
        <select id="room_type_id" name="room_type_id" required class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
            @foreach ($roomTypes as $type)
                <option value="{{ $type->id }}" @selected(old('room_type_id', $room->room_type_id) == $type->id)>{{ $type->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm mb-1" for="status">Status</label>
        <select id="status" name="status" required class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
            @foreach (\App\Enums\RoomStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(old('status', $room->status?->value ?? 'available') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm mb-1" for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="2"
                  class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">{{ old('notes', $room->notes) }}</textarea>
    </div>
</div>

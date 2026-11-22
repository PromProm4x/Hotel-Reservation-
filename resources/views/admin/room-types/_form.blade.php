<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-sm mb-1" for="name">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $roomType->name) }}" required
               class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm mb-1" for="description">Description</label>
        <textarea id="description" name="description" rows="3"
                  class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">{{ old('description', $roomType->description) }}</textarea>
    </div>
    <div>
        <label class="block text-sm mb-1" for="base_price">Base price / night</label>
        <input id="base_price" type="number" step="0.01" min="0" name="base_price" value="{{ old('base_price', $roomType->base_price) }}" required
               class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm mb-1" for="size_sqm">Size (m²)</label>
        <input id="size_sqm" type="number" min="0" name="size_sqm" value="{{ old('size_sqm', $roomType->size_sqm) }}"
               class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm mb-1" for="capacity_adults">Adults</label>
        <input id="capacity_adults" type="number" min="1" max="10" name="capacity_adults" value="{{ old('capacity_adults', $roomType->capacity_adults ?? 2) }}" required
               class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm mb-1" for="capacity_children">Children</label>
        <input id="capacity_children" type="number" min="0" max="10" name="capacity_children" value="{{ old('capacity_children', $roomType->capacity_children ?? 0) }}" required
               class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm mb-1" for="amenities">Amenities <span class="text-[#75323d]/50">(comma-separated)</span></label>
        <input id="amenities" type="text" name="amenities" value="{{ old('amenities', implode(', ', $roomType->amenities ?? [])) }}"
               class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm mb-1" for="photos">Photo URLs <span class="text-[#75323d]/50">(one per line)</span></label>
        <textarea id="photos" name="photos" rows="3"
                  class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">{{ old('photos', implode("\n", $roomType->photos ?? [])) }}</textarea>
    </div>
    <label class="flex items-center gap-2 text-sm">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $roomType->is_active ?? true)) class="rounded border-[#e9dcc2]">
        Active (bookable online)
    </label>
</div>

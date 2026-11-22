<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name', $service->name ?? '') }}" required maxlength="150"
               class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Price (USD)</label>
        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $service->price ?? '') }}" required
               class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Billing unit</label>
        <select name="unit" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm capitalize">
            @foreach (\App\Models\Service::UNITS as $unit)
                <option value="{{ $unit }}" @selected(old('unit', $service->unit ?? 'per stay') === $unit)>{{ ucfirst($unit) }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end pb-2">
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active ?? true))
                   class="rounded border-[#e9dcc2] text-[#5a1f28]">
            Active (staff can add it to folios)
        </label>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Description</label>
        <textarea name="description" rows="3" maxlength="2000"
                  class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">{{ old('description', $service->description ?? '') }}</textarea>
    </div>
</div>

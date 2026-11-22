@extends('layouts.admin')

@section('title', 'Services')

@section('content')
<div class="flex items-center justify-between">
    <h1 class="text-2xl" style="font-family: 'Playfair Display', serif;">Hotel services</h1>
    <a href="{{ route('admin.services.create') }}" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-4 py-2 text-sm hover:bg-[#75323d]">New service</a>
</div>
<p class="text-sm text-[#75323d]/60 mt-1">Chargeable extras that staff can add to any reservation folio.</p>

<div class="bg-white rounded-xl border border-[#e9dcc2] mt-6 overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="text-left text-xs uppercase tracking-wider text-[#a9853f]">
            <tr>
                <th class="px-5 py-3">Service</th>
                <th class="px-5 py-3">Billing</th>
                <th class="px-5 py-3 text-right">Price</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#f6ecd9]">
            @forelse ($services as $service)
                <tr class="hover:bg-[#fbf6ec]">
                    <td class="px-5 py-3">
                        <span class="font-medium">{{ $service->name }}</span>
                        @if ($service->description)
                            <p class="text-xs text-[#75323d]/60 mt-0.5">{{ \Illuminate\Support\Str::limit($service->description, 80) }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 capitalize">{{ $service->unit }}</td>
                    <td class="px-5 py-3 text-right whitespace-nowrap">USD {{ number_format($service->price, 2) }}</td>
                    <td class="px-5 py-3">
                        @if ($service->is_active)
                            <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 text-emerald-800">Active</span>
                        @else
                            <span class="text-xs px-2 py-1 rounded-full bg-stone-200 text-stone-600">Inactive</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('admin.services.edit', $service) }}" class="text-[#a9853f] hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="inline-block ml-2"
                              onsubmit="return confirm('Delete {{ $service->name }}? Charges already on folios are kept.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-700 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-6 text-[#75323d]/60">No services yet. Create one to start charging extras.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

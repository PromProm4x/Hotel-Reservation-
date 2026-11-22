@extends('layouts.admin')

@section('title', 'Reservation '.$reservation->code)

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <a href="{{ route('admin.reservations.index') }}" class="text-sm text-[#a9853f] hover:underline">&larr; Reservations</a>
        <h1 class="text-2xl mt-1" style="font-family: 'Playfair Display', serif;">{{ $reservation->code }}</h1>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-xs px-3 py-1.5 rounded-full {{ $reservation->status->badgeClasses() }}">{{ $reservation->status->label() }}</span>
        <a href="{{ route('admin.reservations.edit', $reservation) }}" class="rounded-lg border border-[#e9dcc2] bg-white px-4 py-2 text-sm hover:bg-[#f6ecd9]">Edit</a>
        <form method="POST" action="{{ route('admin.reservations.destroy', $reservation) }}"
              onsubmit="return confirm('Delete reservation {{ $reservation->code }}? This frees the room for the dates.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-lg border border-rose-200 text-rose-700 px-4 py-2 text-sm hover:bg-rose-50">Delete</button>
        </form>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-3 mt-6">
    <div class="lg:col-span-2 space-y-6">
        <section class="bg-white rounded-xl border border-[#e9dcc2] p-6">
            <h2 class="text-xs uppercase tracking-widest text-[#a9853f]">Stay details</h2>
            <dl class="mt-4 grid gap-x-8 gap-y-3 sm:grid-cols-2 text-sm">
                <div><dt class="text-[#75323d]/60">Guest</dt><dd class="mt-0.5"><a href="{{ route('admin.guests.show', $reservation->guest) }}" class="text-[#a9853f] hover:underline">{{ $reservation->guest->full_name }}</a></dd></div>
                <div><dt class="text-[#75323d]/60">Contact</dt><dd class="mt-0.5">{{ $reservation->guest->email }}@if($reservation->guest->phone) &middot; {{ $reservation->guest->phone }}@endif</dd></div>
                <div><dt class="text-[#75323d]/60">Room type</dt><dd class="mt-0.5">{{ $reservation->roomType->name }}</dd></div>
                <div><dt class="text-[#75323d]/60">Room</dt><dd class="mt-0.5">{{ $reservation->room?->room_number ?? 'Unassigned' }}</dd></div>
                <div><dt class="text-[#75323d]/60">Check-in</dt><dd class="mt-0.5">{{ $reservation->check_in->format('D, d M Y') }}</dd></div>
                <div><dt class="text-[#75323d]/60">Check-out</dt><dd class="mt-0.5">{{ $reservation->check_out->format('D, d M Y') }} &middot; {{ $reservation->nights }} night(s)</dd></div>
                <div><dt class="text-[#75323d]/60">Party</dt><dd class="mt-0.5">{{ $reservation->adults }} adult(s), {{ $reservation->children }} child(ren)</dd></div>
                <div><dt class="text-[#75323d]/60">Source</dt><dd class="mt-0.5 capitalize">{{ str_replace('_', ' ', $reservation->source) }}</dd></div>
                <div><dt class="text-[#75323d]/60">Booked on</dt><dd class="mt-0.5">{{ $reservation->created_at->format('d M Y, H:i') }}</dd></div>
                <div><dt class="text-[#75323d]/60">Booked by</dt><dd class="mt-0.5">{{ $reservation->user?->name ?? 'Guest (online)' }}</dd></div>
                @if ($reservation->checked_in_at)
                    <div><dt class="text-[#75323d]/60">Checked in at</dt><dd class="mt-0.5">{{ $reservation->checked_in_at->format('d M Y, H:i') }}</dd></div>
                @endif
                @if ($reservation->checked_out_at)
                    <div><dt class="text-[#75323d]/60">Checked out at</dt><dd class="mt-0.5">{{ $reservation->checked_out_at->format('d M Y, H:i') }}</dd></div>
                @endif
            </dl>
            @if ($reservation->special_requests)
                <div class="mt-4 rounded-lg bg-[#f6ecd9] p-4 text-sm">
                    <p class="text-xs uppercase tracking-widest text-[#a9853f]">Special requests</p>
                    <p class="mt-1">{{ $reservation->special_requests }}</p>
                </div>
            @endif
        </section>

        <section class="bg-white rounded-xl border border-[#e9dcc2] p-6">
            <h2 class="text-xs uppercase tracking-widest text-[#a9853f]">Services &amp; extras</h2>
            <table class="w-full text-sm mt-4">
                <thead class="text-left text-xs uppercase tracking-wider text-[#a9853f]">
                    <tr><th class="py-2">Service</th><th class="py-2 text-right">Unit price</th><th class="py-2 text-right">Qty</th><th class="py-2 text-right">Amount</th><th></th></tr>
                </thead>
                <tbody class="divide-y divide-[#f6ecd9]">
                    @forelse ($reservation->serviceLines as $line)
                        <tr>
                            <td class="py-2">{{ $line->name }}</td>
                            <td class="py-2 text-right">{{ $reservation->currency }} {{ number_format($line->unit_price, 2) }}</td>
                            <td class="py-2 text-right">{{ $line->quantity }}</td>
                            <td class="py-2 text-right">{{ $reservation->currency }} {{ number_format($line->amount, 2) }}</td>
                            <td class="py-2 text-right">
                                <form method="POST" action="{{ route('admin.reservations.services.destroy', [$reservation, $line]) }}" onsubmit="return confirm('Remove this service charge?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-700 hover:underline text-xs">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-4 text-[#75323d]/60">No services added to this folio.</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if ($services->isNotEmpty())
                <form method="POST" action="{{ route('admin.reservations.services.store', $reservation) }}" class="mt-5 pt-5 border-t border-[#f6ecd9] grid gap-3 sm:grid-cols-4 items-end">
                    @csrf
                    <div class="sm:col-span-2">
                        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Service</label>
                        <select name="service_id" required class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }} &mdash; {{ number_format($service->price, 2) }} ({{ $service->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Quantity</label>
                        <input type="number" name="quantity" min="1" max="100" value="1" required class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-4 py-2 text-sm hover:bg-[#75323d]">Add service</button>
                </form>
            @else
                <p class="mt-4 text-sm text-[#75323d]/60">No active services. Manage the catalogue under Administration &rarr; Services.</p>
            @endif
        </section>

        <section class="bg-white rounded-xl border border-[#e9dcc2] p-6">
            <h2 class="text-xs uppercase tracking-widest text-[#a9853f]">Payments &mdash; {{ $reservation->payment_status_label }}</h2>
            <table class="w-full text-sm mt-4">
                <thead class="text-left text-xs uppercase tracking-wider text-[#a9853f]">
                    <tr><th class="py-2">Date</th><th class="py-2">Type</th><th class="py-2">Method</th><th class="py-2">Reference</th><th class="py-2">By</th><th class="py-2 text-right">Amount</th><th></th></tr>
                </thead>
                <tbody class="divide-y divide-[#f6ecd9]">
                    @forelse ($reservation->payments as $payment)
                        <tr>
                            <td class="py-2">{{ $payment->paid_at?->format('d M Y') ?? $payment->created_at->format('d M Y') }}</td>
                            <td class="py-2 capitalize">{{ $payment->type }}</td>
                            <td class="py-2">{{ $payment->method_label }}</td>
                            <td class="py-2 text-[#75323d]/60">{{ $payment->reference }}</td>
                            <td class="py-2 text-[#75323d]/60">{{ $payment->recordedBy?->name ?? '—' }}</td>
                            <td class="py-2 text-right">{{ $payment->type === 'refund' ? '−' : '' }}{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                            <td class="py-2 text-right">
                                <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}" onsubmit="return confirm('Remove this payment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-700 hover:underline text-xs">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-4 text-[#75323d]/60">No payments recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <form method="POST" action="{{ route('admin.payments.store', $reservation) }}" class="mt-5 pt-5 border-t border-[#f6ecd9] grid gap-3 sm:grid-cols-6 items-end">
                @csrf
                <div>
                    <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Amount</label>
                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', max($reservation->balance_due, 0)) }}" required class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Type</label>
                    <select name="type" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                        @foreach (\App\Models\Payment::TYPES as $type)
                            <option value="{{ $type }}" class="capitalize">{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Method</label>
                    <select name="method" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                        @foreach (\App\Models\Payment::METHOD_LABELS as $method => $label)
                            <option value="{{ $method }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-[#a9853f] mb-1">Reference</label>
                    <input type="text" name="reference" placeholder="e.g. KHQR txn ID" class="w-full rounded-lg border border-[#e9dcc2] px-3 py-2 text-sm">
                </div>
                <button type="submit" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-4 py-2 text-sm hover:bg-[#75323d]">Record payment</button>
            </form>
        </section>
    </div>

    <aside class="space-y-6">
        <section class="bg-[#2e1013] text-[#e9dcc2] rounded-xl p-6">
            <h2 class="text-xs uppercase tracking-widest text-[#cfae76]">Folio</h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-[#cfae76]">Rate / night</dt><dd>{{ $reservation->currency }} {{ number_format($reservation->nightly_rate, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-[#cfae76]">Room ({{ $reservation->nights }} nights)</dt><dd>{{ $reservation->currency }} {{ number_format($reservation->subtotal, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-[#cfae76]">Services</dt><dd>{{ $reservation->currency }} {{ number_format($reservation->services_amount, 2) }}</dd></div>
                @if ($reservation->discount_amount > 0)
                    <div class="flex justify-between"><dt class="text-[#cfae76]">Discount</dt><dd>&minus; {{ $reservation->currency }} {{ number_format($reservation->discount_amount, 2) }}</dd></div>
                @endif
                <div class="flex justify-between"><dt class="text-[#cfae76]">Tax</dt><dd>{{ $reservation->currency }} {{ number_format($reservation->tax_amount, 2) }}</dd></div>
                <div class="flex justify-between pt-2 border-t border-[#75323d]"><dt class="text-[#cfae76]">Total</dt><dd class="text-lg">{{ $reservation->currency }} {{ number_format($reservation->total_amount, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-[#cfae76]">Paid</dt><dd>{{ $reservation->currency }} {{ number_format($reservation->amount_paid, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-[#cfae76]">Balance</dt><dd>{{ $reservation->currency }} {{ number_format($reservation->balance_due, 2) }}</dd></div>
            </dl>
            <a href="{{ route('bookings.show', $reservation->code) }}" target="_blank" class="mt-4 block text-center rounded-lg border border-[#75323d] text-[#cfae76] py-2 text-xs hover:bg-[#5a1f28]">Open guest page (KHQR payment) &nearr;</a>
        </section>

        <section class="bg-white rounded-xl border border-[#e9dcc2] p-6">
            <h2 class="text-xs uppercase tracking-widest text-[#a9853f]">Actions</h2>
            <div class="mt-4 space-y-2">
                @if ($reservation->status === \App\Enums\ReservationStatus::Pending)
                    <form method="POST" action="{{ route('admin.reservations.status', $reservation) }}">
                        @csrf
                        <input type="hidden" name="action" value="confirm">
                        <button type="submit" class="w-full rounded-lg bg-[#5a1f28] text-[#f6ecd9] py-2 text-sm hover:bg-[#75323d]">Confirm booking</button>
                    </form>
                @endif
                @if ($reservation->canCheckIn())
                    <form method="POST" action="{{ route('admin.frontdesk.check-in', $reservation) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-[#5a1f28] text-[#f6ecd9] py-2 text-sm hover:bg-[#75323d]">Check in</button>
                    </form>
                @endif
                @if ($reservation->canCheckOut())
                    <form method="POST" action="{{ route('admin.frontdesk.check-out', $reservation) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-[#c6a15b] text-[#2e1013] py-2 text-sm hover:bg-[#cfae76]">Check out</button>
                    </form>
                @endif
                @if ($reservation->canBeCancelled())
                    <form method="POST" action="{{ route('admin.reservations.status', $reservation) }}" onsubmit="return confirm('Cancel this reservation?');">
                        @csrf
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" class="w-full rounded-lg border border-rose-200 text-rose-700 py-2 text-sm hover:bg-rose-50">Cancel reservation</button>
                    </form>
                    <form method="POST" action="{{ route('admin.reservations.status', $reservation) }}" onsubmit="return confirm('Mark as no-show?');">
                        @csrf
                        <input type="hidden" name="action" value="no_show">
                        <button type="submit" class="w-full rounded-lg border border-[#e9dcc2] py-2 text-sm hover:bg-[#f6ecd9]">Mark as no-show</button>
                    </form>
                @endif
            </div>
        </section>
    </aside>
</div>
@endsection

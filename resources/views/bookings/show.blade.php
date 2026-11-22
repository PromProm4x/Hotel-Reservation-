@extends('layouts.app')

@section('title', 'Reservation '.$reservation->code.' — De La Renaissance')

@section('content')
@php
    $bakongId = \App\Models\HotelSetting::get('bakong_account_id');
    $bakongName = \App\Models\HotelSetting::get('bakong_account_name');
    $khqrEnabled = \App\Models\HotelSetting::get('khqr_enabled') === '1'
        && $bakongId
        && $reservation->balance_due > 0
        && in_array($reservation->status->value, ['pending', 'confirmed', 'checked_in'], true);
    $khqrPayload = 'KHQR|'.$bakongId.'|'.$bakongName.'|'.$reservation->currency.' '.number_format($reservation->balance_due, 2, '.', '').'|'.$reservation->code;
@endphp
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-12">
    <div class="bg-white rounded-2xl border border-[#e9dcc2] shadow-sm overflow-hidden">
        <div class="bg-[#2e1013] text-[#e9dcc2] px-8 py-6 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-widest text-[#cfae76]">Reservation</p>
                <h1 class="text-2xl mt-1" style="font-family: 'Playfair Display', serif;">{{ $reservation->code }}</h1>
            </div>
            <span class="text-xs px-3 py-1.5 rounded-full {{ $reservation->status->badgeClasses() }}">{{ $reservation->status->label() }}</span>
        </div>

        <div class="p-8">
            <dl class="grid gap-x-8 gap-y-4 sm:grid-cols-2 text-sm">
                <div><dt class="text-[#a9853f] text-xs uppercase tracking-widest">Guest</dt><dd class="mt-1">{{ $reservation->guest->full_name }}</dd></div>
                <div><dt class="text-[#a9853f] text-xs uppercase tracking-widest">Room</dt><dd class="mt-1">{{ $reservation->roomType->name }}@if($reservation->room) — Room {{ $reservation->room->room_number }}@endif</dd></div>
                <div><dt class="text-[#a9853f] text-xs uppercase tracking-widest">Check-in</dt><dd class="mt-1">{{ $reservation->check_in->format('D, d M Y') }}</dd></div>
                <div><dt class="text-[#a9853f] text-xs uppercase tracking-widest">Check-out</dt><dd class="mt-1">{{ $reservation->check_out->format('D, d M Y') }}</dd></div>
                <div><dt class="text-[#a9853f] text-xs uppercase tracking-widest">Party</dt><dd class="mt-1">{{ $reservation->adults }} adult(s)@if($reservation->children), {{ $reservation->children }} child(ren)@endif</dd></div>
                <div><dt class="text-[#a9853f] text-xs uppercase tracking-widest">Payment</dt><dd class="mt-1">{{ $reservation->payment_status_label }}</dd></div>
            </dl>

            @if ($reservation->special_requests)
                <div class="mt-6 rounded-lg bg-[#f6ecd9] p-4 text-sm">
                    <p class="text-xs uppercase tracking-widest text-[#a9853f]">Special requests</p>
                    <p class="mt-1">{{ $reservation->special_requests }}</p>
                </div>
            @endif

            <div class="mt-8 border-t border-[#f6ecd9] pt-6">
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt>Rate × {{ $reservation->nights }} night(s)</dt><dd>{{ $reservation->currency }} {{ number_format($reservation->subtotal, 2) }}</dd></div>
                    @foreach ($reservation->serviceLines as $line)
                        <div class="flex justify-between"><dt>{{ $line->name }} × {{ $line->quantity }}</dt><dd>{{ $reservation->currency }} {{ number_format($line->amount, 2) }}</dd></div>
                    @endforeach
                    @if ($reservation->discount_amount > 0)
                        <div class="flex justify-between text-emerald-700"><dt>Discount</dt><dd>− {{ $reservation->currency }} {{ number_format($reservation->discount_amount, 2) }}</dd></div>
                    @endif
                    <div class="flex justify-between"><dt>Tax</dt><dd>{{ $reservation->currency }} {{ number_format($reservation->tax_amount, 2) }}</dd></div>
                    <div class="flex justify-between text-base font-medium pt-2 border-t border-[#f6ecd9]"><dt>Total</dt><dd>{{ $reservation->currency }} {{ number_format($reservation->total_amount, 2) }}</dd></div>
                    @if ($reservation->amount_paid > 0)
                        <div class="flex justify-between text-emerald-700"><dt>Paid</dt><dd>− {{ $reservation->currency }} {{ number_format($reservation->amount_paid, 2) }}</dd></div>
                        <div class="flex justify-between"><dt>Balance due</dt><dd>{{ $reservation->currency }} {{ number_format($reservation->balance_due, 2) }}</dd></div>
                    @endif
                </dl>
            </div>

            @if ($khqrEnabled)
                <div class="mt-8 border-t border-[#f6ecd9] pt-6">
                    <p class="text-xs uppercase tracking-widest text-[#a9853f]">Pay by KHQR (Bakong)</p>
                    <div class="mt-4 flex flex-col sm:flex-row gap-6 sm:items-center">
                        <div id="khqr-code" data-payload="{{ $khqrPayload }}" class="shrink-0 rounded-xl border border-[#e9dcc2] bg-white p-3 text-xs text-[#75323d]/60"></div>
                        <div class="text-sm">
                            <p>Scan with any Bakong member app (ABA, ACLEDA, Wing…) to pay the balance of <span class="font-medium">{{ $reservation->currency }} {{ number_format($reservation->balance_due, 2) }}</span>.</p>
                            <p class="mt-2 text-[#75323d]/70">Account: {{ $bakongName }} · {{ $bakongId }}</p>
                            <p class="mt-1 text-[#75323d]/70">Please include reference <span class="font-medium">{{ $reservation->code }}</span> so we can match your payment. You can also pay by cash or card at the front desk.</p>
                        </div>
                    </div>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
                <script>
                    (function () {
                        var el = document.getElementById('khqr-code');
                        if (! el) { return; }
                        if (typeof qrcode === 'undefined') {
                            el.textContent = 'QR unavailable — please pay at the front desk.';
                            return;
                        }
                        var qr = qrcode(0, 'M');
                        qr.addData(el.dataset.payload);
                        qr.make();
                        el.innerHTML = qr.createSvgTag(4, 8);
                    })();
                </script>
            @endif

            @auth
                @if ($reservation->canBeCancelled() && ($reservation->user_id === auth()->id() || $reservation->guest?->user_id === auth()->id()))
                    <form method="POST" action="{{ route('bookings.cancel', $reservation->code) }}" class="mt-8"
                          onsubmit="return confirm('Cancel this reservation?');">
                        @csrf
                        <button type="submit" class="text-sm text-rose-700 hover:underline">Cancel this reservation</button>
                    </form>
                @endif
            @endauth
        </div>
    </div>
</div>
@endsection

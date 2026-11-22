<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Reservation $reservation,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $r = $this->reservation;

        return (new MailMessage)
            ->subject("Your reservation at De La Renaissance — {$r->code}")
            ->greeting("Dear {$r->guest->full_name},")
            ->line('Thank you for choosing De La Renaissance. Your reservation details are below.')
            ->line("Confirmation code: **{$r->code}**")
            ->line("Room: {$r->roomType->name}")
            ->line("Check-in: {$r->check_in->format('D, d M Y')} — Check-out: {$r->check_out->format('D, d M Y')} ({$r->nights} night(s))")
            ->line("Total: {$r->currency} ".number_format((float) $r->total_amount, 2))
            ->action('View your reservation', route('bookings.show', $r->code))
            ->line('We look forward to welcoming you.');
    }
}

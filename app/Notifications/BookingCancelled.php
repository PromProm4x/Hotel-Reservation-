<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification implements ShouldQueue
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
            ->subject("Reservation cancelled — {$r->code}")
            ->greeting("Dear {$r->guest->full_name},")
            ->line("Your reservation {$r->code} for the {$r->roomType->name} ".
                "({$r->check_in->format('d M Y')} – {$r->check_out->format('d M Y')}) has been cancelled.")
            ->line('If a refund applies, our team will process it shortly.')
            ->line('We hope to welcome you to De La Renaissance another time.');
    }
}

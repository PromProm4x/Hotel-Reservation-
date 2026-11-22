<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckInReminder extends Notification implements ShouldQueue
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
            ->subject('We look forward to welcoming you tomorrow — De La Renaissance')
            ->greeting("Dear {$r->guest->full_name},")
            ->line("A friendly reminder that your stay in our {$r->roomType->name} begins tomorrow, {$r->check_in->format('D, d M Y')}.")
            ->line('Check-in opens at '.\App\Models\HotelSetting::get('check_in_time', '14:00').'.')
            ->line("Confirmation code: **{$r->code}**")
            ->line('Safe travels — we look forward to your arrival.');
    }
}

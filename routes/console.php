<?php

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Notifications\CheckInReminder;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
| Run `php artisan schedule:work` locally (or a cron entry in production).
*/

// Send check-in reminders for tomorrow's arrivals every day at 10:00.
Schedule::call(function () {
    Reservation::query()
        ->whereDate('check_in', now()->addDay()->toDateString())
        ->where('status', ReservationStatus::Confirmed->value)
        ->with(['guest', 'roomType'])
        ->get()
        ->each(function (Reservation $reservation) {
            if ($reservation->guest?->email) {
                Notification::route('mail', $reservation->guest->email)
                    ->notify(new CheckInReminder($reservation));
            }
        });
})->dailyAt('10:00')->name('send-check-in-reminders');

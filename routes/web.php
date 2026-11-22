<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FrontDeskController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Admin\ReservationServiceLineController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomSearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public (guest-facing)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [RoomSearchController::class, 'index'])->name('rooms.search');
Route::get('/rooms/{roomType:slug}', [RoomSearchController::class, 'show'])->name('rooms.show');

Route::get('/book/{roomType:slug}', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/book', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/booking/{code}', [BookingController::class, 'show'])->name('bookings.show');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Signed-in guests
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/booking/{code}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

/*
|--------------------------------------------------------------------------
| Staff & Admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin|staff'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Reservations & front desk (staff + admin)
        Route::get('/reservations', [AdminReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/calendar', [AdminReservationController::class, 'calendar'])->name('reservations.calendar');
        Route::get('/reservations/create', [AdminReservationController::class, 'create'])->name('reservations.create');
        Route::post('/reservations', [AdminReservationController::class, 'store'])->name('reservations.store');
        Route::get('/reservations/{reservation}', [AdminReservationController::class, 'show'])->name('reservations.show');
        Route::post('/reservations/{reservation}/status', [AdminReservationController::class, 'updateStatus'])->name('reservations.status');
        Route::get('/reservations/{reservation}/edit', [AdminReservationController::class, 'edit'])->name('reservations.edit');
        Route::put('/reservations/{reservation}', [AdminReservationController::class, 'update'])->name('reservations.update');
        Route::delete('/reservations/{reservation}', [AdminReservationController::class, 'destroy'])->name('reservations.destroy');

        // Folio service charges (staff + admin)
        Route::post('/reservations/{reservation}/services', [ReservationServiceLineController::class, 'store'])->name('reservations.services.store');
        Route::delete('/reservations/{reservation}/services/{line}', [ReservationServiceLineController::class, 'destroy'])->name('reservations.services.destroy');

        Route::get('/frontdesk', [FrontDeskController::class, 'board'])->name('frontdesk.board');
        Route::post('/frontdesk/{reservation}/check-in', [FrontDeskController::class, 'checkIn'])->name('frontdesk.check-in');
        Route::post('/frontdesk/{reservation}/check-out', [FrontDeskController::class, 'checkOut'])->name('frontdesk.check-out');
        Route::post('/frontdesk/rooms/{room}/status', [FrontDeskController::class, 'updateRoomStatus'])->name('frontdesk.room-status');

        // Payments (staff + admin)
        Route::post('/reservations/{reservation}/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::delete('/reservations/{reservation}/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

        // Guests CRM (staff + admin)
        Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');
        Route::get('/guests/{guest}', [GuestController::class, 'show'])->name('guests.show');
        Route::get('/guests/{guest}/edit', [GuestController::class, 'edit'])->name('guests.edit');
        Route::put('/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
        Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');

        // Room inventory management (admin only)
        Route::middleware('role:admin')->group(function () {
            Route::resource('room-types', RoomTypeController::class)
                ->except(['show'])
                ->parameters(['room-types' => 'room_type']);
            Route::resource('rooms', RoomController::class)->except(['show']);
            Route::resource('services', ServiceController::class)->except(['show']);
        });
    });

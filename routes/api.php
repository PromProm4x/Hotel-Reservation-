<?php

use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\ReservationController;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1 — ready for a future mobile app (Laravel Sanctum)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public catalogue + availability
    Route::get('/room-types', function () {
        return RoomType::query()->active()->orderBy('base_price')->get();
    });

    Route::get('/availability', AvailabilityController::class);

    // Authenticated (token) endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', fn (Request $request) => $request->user());
        Route::post('/reservations', [ReservationController::class, 'store']);
        Route::get('/reservations/{code}', [ReservationController::class, 'show']);
    });
});

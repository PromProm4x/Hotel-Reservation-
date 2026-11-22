<?php

namespace Database\Factories;

use App\Enums\ReservationStatus;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('-1 month', '+2 months');
        $nights = fake()->numberBetween(1, 7);
        $checkOut = (clone $checkIn)->modify("+{$nights} days");
        $rate = fake()->randomFloat(2, 80, 600);
        $subtotal = round($rate * $nights, 2);
        $tax = round($subtotal * 0.10, 2);

        return [
            'code' => Reservation::generateCode(),
            'room_type_id' => RoomType::factory(),
            'room_id' => Room::factory(),
            'guest_id' => Guest::factory(),
            'user_id' => null,
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'adults' => fake()->numberBetween(1, 3),
            'children' => fake()->numberBetween(0, 2),
            'status' => ReservationStatus::Confirmed,
            'source' => fake()->randomElement(['online', 'walk_in', 'phone']),
            'special_requests' => fake()->boolean(30) ? fake()->sentence() : null,
            'nightly_rate' => $rate,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => round($subtotal + $tax, 2),
            'currency' => 'USD',
        ];
    }
}

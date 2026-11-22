<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'recorded_by' => null,
            'amount' => fake()->randomFloat(2, 50, 500),
            'currency' => 'USD',
            'method' => fake()->randomElement(['cash', 'card', 'bank_transfer']),
            'type' => fake()->randomElement(['deposit', 'full']),
            'status' => PaymentStatus::Completed,
            'reference' => strtoupper(fake()->bothify('RCPT-######')),
            'paid_at' => now(),
        ];
    }
}

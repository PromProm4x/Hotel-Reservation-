<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Guest>
 */
class GuestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'nationality' => fake()->country(),
            'id_document_type' => 'passport',
            'id_document_number' => strtoupper(fake()->bothify('??#######')),
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
            'preferences' => fake()->randomElements(
                ['High floor', 'Quiet room', 'Early check-in', 'Extra pillows', 'Feather-free bedding', 'Near elevator'],
                fake()->numberBetween(0, 3),
            ),
        ];
    }
}

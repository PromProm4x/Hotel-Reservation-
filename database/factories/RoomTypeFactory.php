<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\RoomType>
 */
class RoomTypeFactory extends Factory
{
    public function definition(): array
    {
        $name = ucwords(fake()->unique()->words(2, true)).' Room';

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'base_price' => fake()->randomFloat(2, 80, 600),
            'capacity_adults' => fake()->numberBetween(1, 4),
            'capacity_children' => fake()->numberBetween(0, 2),
            'size_sqm' => fake()->numberBetween(18, 90),
            'amenities' => fake()->randomElements(
                ['Wi-Fi', 'Air conditioning', 'Minibar', 'Safe', 'Bathtub', 'Balcony', 'City view', 'Coffee machine'],
                4,
            ),
            'photos' => [],
            'is_active' => true,
        ];
    }
}

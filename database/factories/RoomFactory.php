<?php

namespace Database\Factories;

use App\Enums\RoomStatus;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    public function definition(): array
    {
        $floor = fake()->numberBetween(1, 5);

        return [
            'room_type_id' => RoomType::factory(),
            'room_number' => $floor.str_pad((string) fake()->unique()->numberBetween(1, 99), 2, '0', STR_PAD_LEFT),
            'floor' => $floor,
            'status' => RoomStatus::Available,
            'notes' => null,
        ];
    }
}

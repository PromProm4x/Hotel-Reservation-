<?php

namespace Database\Seeders;

use App\Enums\RoomStatus;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // 5 floors, 170 rooms in total:
        // Deluxe Double 81, Deluxe Twin 81, Deluxe Triple 8.
        $plan = [
            1 => ['Deluxe Double' => 17, 'Deluxe Twin' => 17],
            2 => ['Deluxe Double' => 16, 'Deluxe Twin' => 16, 'Deluxe Triple' => 2],
            3 => ['Deluxe Double' => 16, 'Deluxe Twin' => 16, 'Deluxe Triple' => 2],
            4 => ['Deluxe Double' => 16, 'Deluxe Twin' => 16, 'Deluxe Triple' => 2],
            5 => ['Deluxe Double' => 16, 'Deluxe Twin' => 16, 'Deluxe Triple' => 2],
        ];

        $typeIds = RoomType::query()->pluck('id', 'name');

        foreach ($plan as $floor => $mix) {
            $number = 0;

            foreach ($mix as $typeName => $count) {
                if (! isset($typeIds[$typeName])) {
                    continue;
                }

                for ($i = 1; $i <= $count; $i++) {
                    $number++;
                    $roomNumber = $floor.str_pad((string) $number, 2, '0', STR_PAD_LEFT);

                    Room::query()->updateOrCreate(
                        ['room_number' => $roomNumber],
                        [
                            'room_type_id' => $typeIds[$typeName],
                            'floor' => $floor,
                            'status' => RoomStatus::Available,
                        ],
                    );
                }
            }
        }
    }
}

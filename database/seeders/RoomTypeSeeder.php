<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Deluxe Double',
                'description' => 'A refined double room bathed in warm light, with plush bedding, a work desk, and elegant renaissance-inspired tones.',
                'base_price' => 35.00,
                'capacity_adults' => 2,
                'capacity_children' => 1,
                'size_sqm' => 32,
                'amenities' => ['Wi-Fi', 'Air conditioning', 'Hot shower', 'Minibar', 'Smart TV', 'In-room safe', 'Bathrobes & slippers'],
                'photos' => ['/images/rooms/deluxe-double.jpg'],
            ],
            [
                'name' => 'Deluxe Twin',
                'description' => 'Two comfortable twin beds with crisp linens and gold accents — ideal for friends or colleagues travelling together.',
                'base_price' => 35.00,
                'capacity_adults' => 2,
                'capacity_children' => 1,
                'size_sqm' => 32,
                'amenities' => ['Wi-Fi', 'Air conditioning', 'Hot shower', 'Minibar', 'Smart TV', 'In-room safe', 'Writing desk'],
                'photos' => ['/images/rooms/deluxe-twin.jpg'],
            ],
            [
                'name' => 'Deluxe Triple',
                'description' => 'Three plush single beds dressed in gold and burgundy — perfect for families or small groups travelling together.',
                'base_price' => 50.00,
                'capacity_adults' => 3,
                'capacity_children' => 1,
                'size_sqm' => 42,
                'amenities' => ['Wi-Fi', 'Air conditioning', 'Hot shower', 'Minibar', 'Smart TV', 'In-room safe', 'Work desk'],
                'photos' => ['/images/rooms/deluxe-triple.jpg'],
            ],
            [
                'name' => 'VIP',
                'description' => 'Our VIP room: a spacious retreat with a grand king bed, lounge corner, dressing table, and a private balcony for in-room dining.',
                'base_price' => 70.00,
                'capacity_adults' => 2,
                'capacity_children' => 2,
                'size_sqm' => 55,
                'amenities' => ['Wi-Fi', 'Air conditioning', 'King bed', 'Balcony dining', 'Bathtub', 'Minibar', 'Smart TV', 'Bathrobes & slippers'],
                'photos' => ['/images/rooms/vip-suite.jpg'],
            ],
        ];

        foreach ($types as $type) {
            RoomType::query()->updateOrCreate(
                ['name' => $type['name']],
                [...$type, 'photos' => $type['photos'] ?? [], 'is_active' => true],
            );
        }

        // Remove legacy room types that are no longer offered.
        $legacy = RoomType::query()
            ->whereIn('name', ['Classic Single', 'Family Deluxe', 'Renaissance Suite', 'Royal Penthouse'])
            ->get();

        foreach ($legacy as $roomType) {
            $roomType->rooms()->delete();
            $roomType->delete();
        }
    }
}

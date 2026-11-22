<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Breakfast', 'unit' => 'per person', 'price' => 12.00, 'description' => 'Continental breakfast served 6:30–10:30 in Le Jardin.'],
            ['name' => 'Airport transfer', 'unit' => 'per stay', 'price' => 25.00, 'description' => 'Private sedan pick-up or drop-off, Phnom Penh International Airport.'],
            ['name' => 'Laundry', 'unit' => 'per stay', 'price' => 8.00, 'description' => 'Same-day laundry and pressing, per bag.'],
            ['name' => 'Spa treatment', 'unit' => 'per person', 'price' => 45.00, 'description' => '60-minute signature massage at the Renaissance Spa.'],
            ['name' => 'Late check-out', 'unit' => 'per stay', 'price' => 30.00, 'description' => 'Guaranteed late check-out until 15:00, subject to availability.'],
            ['name' => 'Extra bed', 'unit' => 'per night', 'price' => 15.00, 'description' => 'Rollaway bed with full linen set.'],
            ['name' => 'Minibar restock', 'unit' => 'per stay', 'price' => 20.00, 'description' => 'Full minibar selection restocked daily.'],
        ];

        foreach ($services as $service) {
            Service::query()->firstOrCreate(
                ['name' => $service['name']],
                $service,
            );
        }
    }
}

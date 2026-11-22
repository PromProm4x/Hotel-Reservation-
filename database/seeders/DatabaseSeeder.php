<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            HotelSettingSeeder::class,
            RoomTypeSeeder::class,
            RoomSeeder::class,
            ServiceSeeder::class,
            DemoBookingSeeder::class,
        ]);
    }
}

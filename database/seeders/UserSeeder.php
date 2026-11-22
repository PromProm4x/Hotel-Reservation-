<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@delarenaissance.test'],
            ['name' => 'Hotel Administrator', 'password' => 'password'],
        );
        $admin->syncRoles(['admin']);

        $staff = User::query()->firstOrCreate(
            ['email' => 'frontdesk@delarenaissance.test'],
            ['name' => 'Front Desk Receptionist', 'password' => 'password'],
        );
        $staff->syncRoles(['staff']);

        $guest = User::query()->firstOrCreate(
            ['email' => 'guest@delarenaissance.test'],
            ['name' => 'Demo Guest', 'password' => 'password'],
        );
        $guest->syncRoles(['guest']);
    }
}

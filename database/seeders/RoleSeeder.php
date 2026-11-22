<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'manage rooms',
            'manage room types',
            'manage reservations',
            'manage payments',
            'manage guests',
            'view dashboard',
            'check in guests',
            'check out guests',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Admin gets everything via the Gate::before rule in AppServiceProvider.
        Role::findOrCreate('admin');

        $staff = Role::findOrCreate('staff');
        $staff->syncPermissions([
            'manage reservations',
            'manage payments',
            'manage guests',
            'view dashboard',
            'check in guests',
            'check out guests',
        ]);

        Role::findOrCreate('guest');
    }
}

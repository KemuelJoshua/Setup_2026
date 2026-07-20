<?php

namespace Database\Seeders\permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'admin view roles',
            'admin create roles',
            'admin update roles',
            'admin delete roles',

            'admin view user',
            'admin create user',
            'admin update user',
            'admin delete user',

            'admin view school-year',
            'admin create school-year',
            'admin update school-year',
            'admin change-status school-year',
            'admin delete school-year',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

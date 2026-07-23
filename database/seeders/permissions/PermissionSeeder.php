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

            'admin view semesters',
            'admin create semesters',
            'admin update semesters',
            'admin delete semesters',

            'admin view grade levels',
            'admin create grade levels',
            'admin update grade levels',
            'admin delete grade levels',

            'admin view sections',
            'admin create sections',
            'admin update sections',
            'admin delete sections',

            'admin view subjects',
            'admin create subjects',
            'admin update subjects',
            'admin delete subjects',

            'admin view programs',
            'admin create programs',
            'admin update programs',
            'admin delete programs',

            'admin view curricula',
            'admin create curricula',
            'admin update curricula',
            'admin delete curricula',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

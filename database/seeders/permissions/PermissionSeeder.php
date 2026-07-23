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

            'admin view academic terms',
            'admin create academic terms',
            'admin update academic terms',
            'admin delete academic terms',

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

        $renamedPermissions = [
            'admin view semesters' => 'admin view academic terms',
            'admin create semesters' => 'admin create academic terms',
            'admin update semesters' => 'admin update academic terms',
            'admin delete semesters' => 'admin delete academic terms',
        ];

        foreach ($renamedPermissions as $oldName => $newName) {
            $oldPermission = Permission::query()
                ->where('name', $oldName)
                ->where('guard_name', 'web')
                ->first();

            if ($oldPermission && ! Permission::query()->where('name', $newName)->where('guard_name', 'web')->exists()) {
                $oldPermission->update(['name' => $newName]);
            }
        }

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

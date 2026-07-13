<?php

namespace Database\Seeders\permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::findOrCreate('Superadmin', 'web');
        Role::findOrCreate('Teacher', 'web');
        Role::findOrCreate('Student', 'web');

        $superadmin = Role::findOrCreate('Superadmin', 'web');
        $superadmin->syncPermissions(Permission::all());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

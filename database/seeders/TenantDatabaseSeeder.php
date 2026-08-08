<?php

namespace Database\Seeders;

use Database\Seeders\permissions\PermissionSeeder;
use Database\Seeders\permissions\RoleSeeder;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);
    }
}

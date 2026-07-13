<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\permissions\PermissionSeeder;
use Database\Seeders\permissions\RoleSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);

        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
        ]);

        $user->assignRole('Superadmin');
    }
}

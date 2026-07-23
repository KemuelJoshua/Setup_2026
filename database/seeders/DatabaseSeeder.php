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
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            SchoolYearSeeder::class,
            AcademicTermSeeder::class,
            GradingPeriodSeeder::class,
            GradeLevelSeeder::class,
            SectionSeeder::class,
            SubjectSeeder::class,
            ProgramSeeder::class,
            CurriculumSeeder::class,
        ]);

        $user = User::query()->firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => 'password',
            ],
        );

        $user->assignRole('Superadmin');
    }
}

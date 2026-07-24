<?php

namespace Database\Seeders;

use App\Models\Academics\EducationalLevel;
use Illuminate\Database\Seeder;

class EducationalLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            'Elementary School',
            'Junior High School',
            'Senior High School',
            'Higher Education',
        ] as $name) {
            EducationalLevel::query()->firstOrCreate(['name' => $name]);
        }
    }
}

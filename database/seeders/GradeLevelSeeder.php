<?php

namespace Database\Seeders;

use App\Models\Academics\GradeLevel;
use Illuminate\Database\Seeder;

class GradeLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(7, 12) as $grade) {
            GradeLevel::query()->firstOrCreate([
                'name' => "Grade {$grade}",
            ]);
        }
    }
}

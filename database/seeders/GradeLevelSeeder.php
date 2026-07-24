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
        foreach ([
            ...array_map(
                static fn (int $year): string => "Year {$year}",
                range(1, 10),
            ),
            'Grade 7',
            'Grade 8',
            'Grade 9',
            'Grade 10',
            'Grade 11',
            'Grade 12',
        ] as $name) {
            GradeLevel::query()->firstOrCreate(['name' => $name]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Academics\EducationalLevel;
use App\Models\Academics\GradeLevel;
use Illuminate\Database\Seeder;

class GradeLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educationalLevels = EducationalLevel::query()
            ->whereIn('name', [
                'Junior High School',
                'Senior High School',
                'Higher Education',
            ])
            ->pluck('id', 'name');

        $gradeLevels = [
            ...array_map(
                static fn (int $year): array => [
                    'name' => "Year {$year}",
                    'educational_level_id' => $educationalLevels['Higher Education'],
                ],
                range(1, 10),
            ),
            ...array_map(
                static fn (int $grade): array => [
                    'name' => "Grade {$grade}",
                    'educational_level_id' => $educationalLevels['Junior High School'],
                ],
                range(7, 10),
            ),
            ...array_map(
                static fn (int $grade): array => [
                    'name' => "Grade {$grade}",
                    'educational_level_id' => $educationalLevels['Senior High School'],
                ],
                range(11, 12),
            ),
        ];

        foreach ($gradeLevels as $gradeLevel) {
            GradeLevel::query()->updateOrCreate(
                ['name' => $gradeLevel['name']],
                ['educational_level_id' => $gradeLevel['educational_level_id']],
            );
        }
    }
}

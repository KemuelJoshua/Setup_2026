<?php

namespace Database\Seeders;

use App\Models\Academics\EducationalLevel;
use App\Models\Academics\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educationalLevels = EducationalLevel::query()->pluck('id', 'name');

        $programs = [
            [
                'educational_level_id' => $educationalLevels['Higher Education'],
                'code' => 'BSIT',
                'name' => 'Bachelor of Science in Information Technology',
                'description' => 'Four-year information technology program.',
                'status' => 'Active',
            ],
            [
                'educational_level_id' => $educationalLevels['Junior High School'],
                'code' => 'JHS',
                'name' => 'Junior High School',
                'description' => 'Grades 7 to 10 basic education program.',
                'status' => 'Active',
            ],
            [
                'educational_level_id' => $educationalLevels['Senior High School'],
                'code' => 'STEM',
                'name' => 'Science, Technology, Engineering, and Mathematics',
                'description' => 'Senior high school academic strand.',
                'status' => 'Active',
            ],
            [
                'educational_level_id' => $educationalLevels['Senior High School'],
                'code' => 'ABM',
                'name' => 'Accountancy, Business, and Management',
                'description' => 'Senior high school academic strand.',
                'status' => 'Active',
            ],
            [
                'educational_level_id' => $educationalLevels['Senior High School'],
                'code' => 'HUMSS',
                'name' => 'Humanities and Social Sciences',
                'description' => 'Senior high school academic strand.',
                'status' => 'Active',
            ],
            [
                'educational_level_id' => $educationalLevels['Senior High School'],
                'code' => 'TVL',
                'name' => 'Technical-Vocational-Livelihood',
                'description' => 'Senior high school technical-vocational track.',
                'status' => 'Active',
            ],
        ];

        foreach ($programs as $program) {
            Program::query()->updateOrCreate(
                ['code' => $program['code']],
                $program,
            );
        }
    }
}

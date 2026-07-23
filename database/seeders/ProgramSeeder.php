<?php

namespace Database\Seeders;

use App\Models\Academics\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'code' => 'JHS',
                'name' => 'Junior High School',
                'description' => 'Grades 7 to 10 basic education program.',
                'status' => 'Active',
            ],
            [
                'code' => 'STEM',
                'name' => 'Science, Technology, Engineering, and Mathematics',
                'description' => 'Senior high school academic strand.',
                'status' => 'Active',
            ],
            [
                'code' => 'ABM',
                'name' => 'Accountancy, Business, and Management',
                'description' => 'Senior high school academic strand.',
                'status' => 'Active',
            ],
            [
                'code' => 'HUMSS',
                'name' => 'Humanities and Social Sciences',
                'description' => 'Senior high school academic strand.',
                'status' => 'Active',
            ],
            [
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

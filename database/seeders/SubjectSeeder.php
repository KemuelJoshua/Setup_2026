<?php

namespace Database\Seeders;

use App\Models\Academics\EducationalLevel;
use App\Models\Academics\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educationalLevel = EducationalLevel::query()
            ->where('name', 'Junior High School')
            ->firstOrFail();

        foreach ([
            'English',
            'Filipino',
            'Mathematics',
            'Science',
            'Araling Panlipunan',
            'MAPEH',
            'Technology and Livelihood Education',
            'Values Education',
        ] as $name) {
            Subject::query()->updateOrCreate(
                ['name' => $name],
                ['educational_level_id' => $educationalLevel->getKey()],
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Academics\Curriculum;
use App\Models\Academics\GradeLevel;
use App\Models\Academics\Semester;
use App\Models\Academics\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurriculumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $curriculum = Curriculum::query()->updateOrCreate(
                ['code' => 'JHS-2026'],
                [
                    'name' => 'Junior High School Curriculum 2026',
                    'effective_year' => 2026,
                    'description' => 'Standard Junior High School curriculum.',
                    'status' => 'Active',
                ],
            );

            $gradeLevel = GradeLevel::query()
                ->where('name', 'Grade 7')
                ->firstOrFail();

            $semesters = Semester::query()
                ->whereIn('code', ['1ST', '2ND'])
                ->orderBy('id')
                ->get();

            $subjects = Subject::query()
                ->whereIn('name', [
                    'English',
                    'Filipino',
                    'Mathematics',
                    'Science',
                    'Araling Panlipunan',
                    'MAPEH',
                    'Technology and Livelihood Education',
                    'Values Education',
                ])
                ->orderBy('id')
                ->get();

            foreach ($semesters as $semester) {
                foreach ($subjects as $sortOrder => $subject) {
                    $curriculum->curriculumSubjects()->updateOrCreate(
                        [
                            'subject_id' => $subject->getKey(),
                            'year_level_id' => $gradeLevel->getKey(),
                            'semester_id' => $semester->getKey(),
                        ],
                        [
                            'is_required' => true,
                            'sort_order' => $sortOrder + 1,
                        ],
                    );
                }
            }
        });
    }
}

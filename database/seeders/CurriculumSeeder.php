<?php

namespace Database\Seeders;

use App\Models\Academics\AcademicPeriod;
use App\Models\Academics\AcademicTermStructure;
use App\Models\Academics\Curriculum;
use App\Models\Academics\GradeLevel;
use App\Models\Academics\Program;
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
            $gradeLevel = GradeLevel::query()
                ->where('name', 'Grade 7')
                ->firstOrFail();

            $juniorHighSchool = AcademicTermStructure::query()
                ->where('code', 'JHS4Q')
                ->firstOrFail();
            $program = Program::query()->where('code', 'JHS')->firstOrFail();
            $curriculum = Curriculum::query()->updateOrCreate(
                ['code' => 'JHS-2026'],
                [
                    'name' => 'Junior High School Curriculum 2026',
                    'program_id' => $program->getKey(),
                    'academic_term_structure_id' => $juniorHighSchool->getKey(),
                    'effective_year' => 2026,
                    'number_of_years' => 4,
                    'description' => 'Standard Junior High School curriculum.',
                    'status' => 'Active',
                ],
            );

            $academicPeriods = AcademicPeriod::query()
                ->whereBelongsTo($juniorHighSchool, 'structure')
                ->roots()
                ->whereIn('code', ['Q1', 'Q2', 'Q3', 'Q4'])
                ->ordered()
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

            foreach ($academicPeriods as $academicPeriod) {
                foreach ($subjects as $sortOrder => $subject) {
                    $curriculum->curriculumSubjects()->updateOrCreate(
                        [
                            'subject_id' => $subject->getKey(),
                            'year_level_id' => $gradeLevel->getKey(),
                            'academic_period_id' => $academicPeriod->getKey(),
                        ],
                        [
                            'is_required' => true,
                            'units' => 1,
                            'lecture_hours' => 1,
                            'laboratory_hours' => 0,
                            'sort_order' => $sortOrder + 1,
                        ],
                    );
                }
            }
        });
    }
}

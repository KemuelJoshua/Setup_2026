<?php

namespace Database\Seeders;

use App\Enums\AcademicStatus;
use App\Enums\AcademicTermStructureType;
use App\Models\Academics\AcademicPeriod;
use App\Models\Academics\AcademicTermStructure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicTermStructureSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $fourGradingPeriods = [
                ['name' => 'Prelim', 'code' => 'PRE'],
                ['name' => 'Midterm', 'code' => 'MID'],
                ['name' => 'Semi-Final', 'code' => 'SEMI'],
                ['name' => 'Final', 'code' => 'FIN'],
            ];
            $semesterTerms = [
                ['name' => 'First Semester', 'code' => 'SEM1'],
                ['name' => 'Second Semester', 'code' => 'SEM2'],
            ];
            $trimesterTerms = [
                ['name' => 'First Trimester', 'code' => 'TRI1'],
                ['name' => 'Second Trimester', 'code' => 'TRI2'],
                ['name' => 'Third Trimester', 'code' => 'TRI3'],
            ];
            $quarterTerms = [
                ['name' => 'First Quarter', 'code' => 'Q1'],
                ['name' => 'Second Quarter', 'code' => 'Q2'],
                ['name' => 'Third Quarter', 'code' => 'Q3'],
                ['name' => 'Fourth Quarter', 'code' => 'Q4'],
            ];

            $structures = [
                [
                    'name' => 'College — Semester (2 Terms, 4 Grading Periods)',
                    'code' => 'C24GP',
                    'type' => AcademicTermStructureType::Semester,
                    'terms' => $semesterTerms,
                    'grading_periods' => $fourGradingPeriods,
                ],
                [
                    'name' => 'College — Semester (2 Terms)',
                    'code' => 'C23GP',
                    'type' => AcademicTermStructureType::Semester,
                    'terms' => $semesterTerms,
                    'grading_periods' => [],
                ],
                [
                    'name' => 'College — Trimester (3 Terms, 4 Grading Periods)',
                    'code' => 'C34GP',
                    'type' => AcademicTermStructureType::Trisem,
                    'terms' => $trimesterTerms,
                    'grading_periods' => $fourGradingPeriods,
                ],
                [
                    'name' => 'College — Trimester (3 Terms)',
                    'code' => 'C33GP',
                    'type' => AcademicTermStructureType::Trisem,
                    'terms' => $trimesterTerms,
                    'grading_periods' => [],
                ],
                [
                    'name' => 'JHS — Quarter',
                    'code' => 'JHS4Q',
                    'type' => AcademicTermStructureType::Quarterly,
                    'terms' => $quarterTerms,
                    'grading_periods' => [],
                ],
                [
                    'name' => 'SHS — Quarter',
                    'code' => 'SHS4Q',
                    'type' => AcademicTermStructureType::Quarterly,
                    'terms' => $quarterTerms,
                    'grading_periods' => [],
                ],
            ];

            foreach ($structures as $structureData) {
                $structure = AcademicTermStructure::query()->updateOrCreate(
                    ['code' => $structureData['code']],
                    [
                        'name' => $structureData['name'],
                        'type' => $structureData['type'],
                        'status' => AcademicStatus::Active,
                    ],
                );

                foreach ($structureData['terms'] as $termIndex => $termData) {
                    $term = AcademicPeriod::query()->updateOrCreate(
                        [
                            'academic_term_structure_id' => $structure->getKey(),
                            'parent_id' => null,
                            'name' => $termData['name'],
                        ],
                        [
                            'code' => $termData['code'],
                            'sequence' => $termIndex + 1,
                            'status' => AcademicStatus::Active,
                        ],
                    );

                    if ($structureData['grading_periods'] === []) {
                        $term->children()->delete();
                    }

                    foreach ($structureData['grading_periods'] as $periodIndex => $periodData) {
                        AcademicPeriod::query()->updateOrCreate(
                            [
                                'academic_term_structure_id' => $structure->getKey(),
                                'parent_id' => $term->getKey(),
                                'name' => $periodData['name'],
                            ],
                            [
                                'code' => $periodData['code'],
                                'sequence' => $periodIndex + 1,
                                'status' => AcademicStatus::Active,
                            ],
                        );
                    }
                }
            }
        });
    }
}

<?php

namespace Database\Seeders;

use App\Models\Academics\AcademicTerm;
use Illuminate\Database\Seeder;

class AcademicTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AcademicTerm::query()
            ->whereIn('code', [
                'SEM',
                'QTR',
                '1ST-PRE',
                '1ST-MID',
                '1ST-SEMI',
                '1ST-FIN',
                '2ND-PRE',
                '2ND-MID',
                '2ND-SEMI',
                '2ND-FIN',
            ])
            ->whereDoesntHave('curriculumSubjects')
            ->delete();

        foreach ([
            ['name' => '1st Semester', 'code' => '1ST'],
            ['name' => '2nd Semester', 'code' => '2ND'],
        ] as $semesterData) {
            AcademicTerm::query()->updateOrCreate(
                ['code' => $semesterData['code']],
                [
                    'name' => $semesterData['name'],
                    'type' => 'Semester',
                ],
            );
        }

        foreach ([
            ['name' => '1st Quarter', 'code' => 'Q1'],
            ['name' => '2nd Quarter', 'code' => 'Q2'],
            ['name' => '3rd Quarter', 'code' => 'Q3'],
            ['name' => '4th Quarter', 'code' => 'Q4'],
        ] as $quarter) {
            AcademicTerm::query()->updateOrCreate(
                ['code' => $quarter['code']],
                [
                    'name' => $quarter['name'],
                    'type' => 'Quarter',
                ],
            );
        }

        AcademicTerm::query()->updateOrCreate(
            ['code' => 'SUM'],
            [
                'name' => 'Summer',
                'type' => 'Not Applicable',
            ],
        );
    }
}

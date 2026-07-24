<?php

namespace Database\Seeders;

use App\Enums\SchoolYearStatus;
use App\Models\Academics\SchoolYear;
use Illuminate\Database\Seeder;

class SchoolYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolYears = [
            [
                'sc_name' => 'School Year 2025-2026',
                'sc_code' => 'SY-2025-2026',
                'sc_start_date' => '2025-06-02',
                'sc_end_date' => '2026-03-31',
                'sc_status' => SchoolYearStatus::CLOSED,
            ],
            [
                'sc_name' => 'School Year 2026-2027',
                'sc_code' => 'SY-2026-2027',
                'sc_start_date' => '2026-06-01',
                'sc_end_date' => '2027-03-31',
                'sc_status' => SchoolYearStatus::ACTIVE,
            ],
            [
                'sc_name' => 'School Year 2027-2028',
                'sc_code' => 'SY-2027-2028',
                'sc_start_date' => '2027-06-07',
                'sc_end_date' => '2028-03-31',
                'sc_status' => SchoolYearStatus::PLANNED,
            ],
        ];

        foreach ($schoolYears as $schoolYear) {
            SchoolYear::query()->updateOrCreate(
                ['sc_code' => $schoolYear['sc_code']],
                $schoolYear,
            );
        }
    }
}

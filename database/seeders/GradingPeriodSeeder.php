<?php

namespace Database\Seeders;

use App\Models\Academics\AcademicTerm;
use App\Models\Academics\GradingPeriod;
use Illuminate\Database\Seeder;

class GradingPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $periods = [
            ['name' => 'Prelim', 'code' => 'PRE', 'sort_order' => 1],
            ['name' => 'Midterm', 'code' => 'MID', 'sort_order' => 2],
            ['name' => 'Semi Final', 'code' => 'SEMI', 'sort_order' => 3],
            ['name' => 'Finals', 'code' => 'FIN', 'sort_order' => 4],
        ];

        AcademicTerm::query()
            ->whereIn('code', ['1ST', '2ND'])
            ->get()
            ->each(function (AcademicTerm $academicTerm) use ($periods): void {
                foreach ($periods as $period) {
                    GradingPeriod::query()->updateOrCreate(
                        [
                            'academic_term_id' => $academicTerm->getKey(),
                            'code' => $period['code'],
                        ],
                        [
                            'name' => $period['name'],
                            'sort_order' => $period['sort_order'],
                        ],
                    );
                }
            });
    }
}

<?php

namespace Database\Seeders;

use App\Models\Academics\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            ['name' => 'First Semester', 'code' => '1ST'],
            ['name' => 'Second Semester', 'code' => '2ND'],
        ] as $semester) {
            Semester::query()->updateOrCreate(
                ['code' => $semester['code']],
                $semester,
            );
        }
    }
}

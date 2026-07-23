<?php

namespace Database\Seeders;

use App\Models\Academics\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            Subject::query()->firstOrCreate(['name' => $name]);
        }
    }
}

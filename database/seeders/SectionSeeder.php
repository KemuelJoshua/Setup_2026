<?php

namespace Database\Seeders;

use App\Models\Academics\EducationalLevel;
use App\Models\Academics\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educationalLevel = EducationalLevel::query()
            ->where('name', 'Junior High School')
            ->firstOrFail();

        foreach (['Section A', 'Section B', 'Section C'] as $name) {
            Section::query()->updateOrCreate(
                ['name' => $name],
                ['educational_level_id' => $educationalLevel->getKey()],
            );
        }
    }
}

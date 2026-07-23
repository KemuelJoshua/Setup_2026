<?php

namespace Database\Seeders;

use App\Models\Academics\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Section A', 'Section B', 'Section C'] as $name) {
            Section::query()->firstOrCreate(['name' => $name]);
        }
    }
}

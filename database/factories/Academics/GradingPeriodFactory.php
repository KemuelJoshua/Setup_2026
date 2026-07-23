<?php

namespace Database\Factories\Academics;

use App\Models\Academics\AcademicTerm;
use App\Models\Academics\GradingPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradingPeriod>
 */
class GradingPeriodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'academic_term_id' => AcademicTerm::factory(),
            'name' => fake()->words(2, true),
            'code' => fake()->unique()->bothify('GP-###'),
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}

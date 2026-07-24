<?php

namespace Database\Factories\Academics;

use App\Enums\AcademicStatus;
use App\Models\Academics\AcademicPeriod;
use App\Models\Academics\AcademicTermStructure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicPeriod>
 */
class AcademicPeriodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'academic_term_structure_id' => AcademicTermStructure::factory(),
            'parent_id' => null,
            'name' => fake()->unique()->words(2, true),
            'code' => fake()->optional()->unique()->bothify('PER-###'),
            'sequence' => fake()->numberBetween(1, 20),
            'status' => AcademicStatus::Active,
        ];
    }

    public function childOf(AcademicPeriod $parent): static
    {
        return $this->state(fn (): array => [
            'academic_term_structure_id' => $parent->academic_term_structure_id,
            'parent_id' => $parent->getKey(),
        ]);
    }
}

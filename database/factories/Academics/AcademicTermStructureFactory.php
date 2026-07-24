<?php

namespace Database\Factories\Academics;

use App\Enums\AcademicStatus;
use App\Enums\AcademicTermStructureType;
use App\Models\Academics\AcademicTermStructure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicTermStructure>
 */
class AcademicTermStructureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'code' => fake()->unique()->bothify('STRUCT-###'),
            'type' => AcademicTermStructureType::Semester,
            'status' => AcademicStatus::Active,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'status' => AcademicStatus::Inactive,
        ]);
    }

    public function quarterly(): static
    {
        return $this->state(fn (): array => [
            'type' => AcademicTermStructureType::Quarterly,
        ]);
    }
}

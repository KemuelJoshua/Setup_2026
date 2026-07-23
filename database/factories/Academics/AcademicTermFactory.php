<?php

namespace Database\Factories\Academics;

use App\Models\Academics\AcademicTerm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicTerm>
 */
class AcademicTermFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'code' => fake()->unique()->bothify('TERM-###'),
            'type' => fake()->randomElement([
                'Quarter',
                'Semester',
                'Not Applicable',
            ]),
        ];
    }
}

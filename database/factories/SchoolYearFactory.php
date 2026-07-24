<?php

namespace Database\Factories;

use App\Enums\SchoolYearStatus;
use App\Models\Academics\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolYear>
 */
class SchoolYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 year', '+1 year');
        $endDate = fake()->dateTimeBetween($startDate, '+2 years');

        return [
            'sc_name' => fake()->unique()->numerify('School Year 20##-20##'),
            'sc_code' => fake()->unique()->bothify('SY-####'),
            'sc_start_date' => $startDate->format('Y-m-d'),
            'sc_end_date' => $endDate->format('Y-m-d'),
            'sc_status' => fake()->randomElement(SchoolYearStatus::cases()),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => [
            'sc_status' => SchoolYearStatus::ACTIVE,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (): array => [
            'sc_status' => SchoolYearStatus::CLOSED,
        ]);
    }

    public function planned(): static
    {
        return $this->state(fn (): array => [
            'sc_status' => SchoolYearStatus::PLANNED,
        ]);
    }
}

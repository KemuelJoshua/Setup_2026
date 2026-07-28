<?php

namespace Database\Factories;

use App\Models\StartupTracking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StartupTracking>
 */
class StartupTrackingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['PAC', 'APPROVAL', 'PROMOTION']),
            'program' => fake()->randomElement(['TECHNICOM 1.0', 'IBED', 'ITECH', 'GALING']),
            'project_title' => fake()->sentence(),
            'proponent_name' => fake()->name(),
            'contact_details' => fake()->safeEmail(),
            'amount' => fake()->randomFloat(2, 90000, 17000000),
            'class' => 'STARTUP',
            'status' => fake()->randomElement(['ONGOING', 'COMPLETED', null]),
            'promotional_assistance' => null,
            'revenue_growth' => null,
            'jobs_created' => null,
            'investments_attracted' => null,
            'market_reach' => null,
            'high_tech_exports' => null,
            'social_impact' => null,
            'next_possible_intervention' => null,
        ];
    }
}

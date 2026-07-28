<?php

namespace Database\Factories;

use App\Models\ProjectImpact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectImpact>
 */
class ProjectImpactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source_sheet' => fake()->randomElement([
                'Pre-Comm & Comm PROJECTS 2021-2025',
                'HIRANG 2021-2025',
                'TECH TRANS 2021-2025',
                'IPRAP 2021-2025',
                'eIPRAP 2021-2025',
            ]),
            'record_number' => (string) fake()->unique()->numberBetween(1, 10000),
            'project_title' => fake()->sentence(),
            'proponent' => fake()->company(),
            'classification' => fake()->randomElement(['MSME', 'RDI', 'Inventor', 'Private']),
            'sub_classification' => null,
            'ip_type' => null,
            'field_of_technology' => null,
            'program_intervention' => fake()->randomElement(['VFP', 'IPRAP', 'HIRANG']),
            'amount_assistance' => fake()->randomFloat(2, 10000, 10000000),
            'date_assistance' => fake()->date(),
            'project_status' => fake()->randomElement(['Completed', 'Ongoing', null]),
            'date_completed' => null,
            'readiness_before' => null,
            'readiness_after' => null,
            'other_interventions' => null,
            'revenue_amount' => null,
            'technology_commercialized' => null,
            'jobs_created' => null,
            'investment_leveraged' => null,
            'efficiency_improved' => null,
            'communities_served' => null,
            'priority_sectors_benefited' => null,
            'ip_assets_utilized' => null,
            'spin_offs_formed' => null,
            'human_capital_developed' => null,
            'other_impacts' => null,
            'impact_narrative' => null,
            'additional_data' => null,
        ];
    }
}

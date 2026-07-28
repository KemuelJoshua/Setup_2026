<?php

namespace App\Models;

use Database\Factories\ProjectImpactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectImpact extends Model
{
    /** @use HasFactory<ProjectImpactFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'source_sheet',
        'record_number',
        'project_title',
        'proponent',
        'classification',
        'sub_classification',
        'ip_type',
        'field_of_technology',
        'program_intervention',
        'amount_assistance',
        'date_assistance',
        'project_status',
        'date_completed',
        'readiness_before',
        'readiness_after',
        'other_interventions',
        'revenue_amount',
        'technology_commercialized',
        'jobs_created',
        'investment_leveraged',
        'efficiency_improved',
        'communities_served',
        'priority_sectors_benefited',
        'ip_assets_utilized',
        'spin_offs_formed',
        'human_capital_developed',
        'other_impacts',
        'impact_narrative',
        'additional_data',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount_assistance' => 'decimal:2',
            'date_assistance' => 'date:Y-m-d',
            'date_completed' => 'date:Y-m-d',
            'additional_data' => 'array',
        ];
    }
}

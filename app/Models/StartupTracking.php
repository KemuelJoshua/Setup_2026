<?php

namespace App\Models;

use Database\Factories\StartupTrackingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StartupTracking extends Model
{
    /** @use HasFactory<StartupTrackingFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'program',
        'project_title',
        'proponent_name',
        'contact_details',
        'amount',
        'class',
        'status',
        'promotional_assistance',
        'revenue_growth',
        'jobs_created',
        'investments_attracted',
        'market_reach',
        'high_tech_exports',
        'social_impact',
        'next_possible_intervention',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'class' => 'STARTUP',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }
}

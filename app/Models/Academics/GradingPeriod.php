<?php

namespace App\Models\Academics;

use Database\Factories\Academics\GradingPeriodFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradingPeriod extends Model
{
    /** @use HasFactory<GradingPeriodFactory> */
    use HasFactory;

    protected $attributes = [
        'sort_order' => 0,
    ];

    protected $fillable = [
        'academic_term_id',
        'name',
        'code',
        'sort_order',
    ];

    /**
     * @return BelongsTo<AcademicTerm, $this>
     */
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}

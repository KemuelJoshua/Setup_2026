<?php

namespace App\Models\Academics;

use Database\Factories\Academics\AcademicTermFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicTerm extends Model
{
    /** @use HasFactory<AcademicTermFactory> */
    use HasFactory;

    protected $attributes = [
        'type' => 'Semester',
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
    ];

    /**
     * @return HasMany<GradingPeriod, $this>
     */
    public function gradingPeriods(): HasMany
    {
        return $this->hasMany(GradingPeriod::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * @return HasMany<CurriculumSubject, $this>
     */
    public function curriculumSubjects(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class);
    }
}

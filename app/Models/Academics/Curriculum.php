<?php

namespace App\Models\Academics;

use Attribute;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(
    'code',
    'name',
    'program_id',
    'academic_term_structure_id',
    'effective_year',
    'number_of_years',
    'description',
    'status',
)]

class Curriculum extends Model
{
    protected $attributes = [
        'number_of_years' => 4,
    ];

    /**
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @return BelongsTo<AcademicTermStructure, $this>
     */
    public function academicTermStructure(): BelongsTo
    {
        return $this->belongsTo(AcademicTermStructure::class);
    }

    /**
     * @return HasMany<CurriculumSubject, $this>
     */
    public function curriculumSubjects(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class)->orderBy('sort_order');
    }

    protected function casts(): array
    {
        return [
            'effective_year' => 'integer',
            'number_of_years' => 'integer',
        ];
    }
}

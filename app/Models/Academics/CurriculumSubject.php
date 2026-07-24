<?php

namespace App\Models\Academics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CurriculumSubject extends Model
{
    protected $fillable = [
        'subject_id',
        'year_level_id',
        'academic_period_id',
        'units',
        'lecture_hours',
        'laboratory_hours',
        'is_required',
        'sort_order',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'sort_order' => 'integer',
            'units' => 'decimal:2',
            'lecture_hours' => 'decimal:2',
            'laboratory_hours' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Curriculum, $this>
     */
    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return BelongsTo<GradeLevel, $this>
     */
    public function yearLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class, 'year_level_id');
    }

    /**
     * @return BelongsTo<AcademicPeriod, $this>
     */
    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    /**
     * @return BelongsToMany<CurriculumSubject, $this>
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'curriculum_subject_prerequisites',
            'curriculum_subject_id',
            'prerequisite_curriculum_subject_id',
        )->withTimestamps();
    }

    /**
     * @return BelongsToMany<CurriculumSubject, $this>
     */
    public function corequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'curriculum_subject_corequisites',
            'curriculum_subject_id',
            'corequisite_curriculum_subject_id',
        )->withTimestamps();
    }
}

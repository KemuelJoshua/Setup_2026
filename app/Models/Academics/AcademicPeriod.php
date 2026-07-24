<?php

namespace App\Models\Academics;

use App\Enums\AcademicStatus;
use Database\Factories\Academics\AcademicPeriodFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $academic_term_structure_id
 * @property int|null $parent_id
 * @property string $name
 * @property string|null $code
 * @property int $sequence
 * @property AcademicStatus $status
 */
#[Fillable(
    'academic_term_structure_id',
    'parent_id',
    'name',
    'code',
    'sequence',
    'status',
)]
class AcademicPeriod extends Model
{
    /** @use HasFactory<AcademicPeriodFactory> */
    use HasFactory;

    protected $attributes = [
        'sequence' => 1,
        'status' => AcademicStatus::Active->value,
    ];

    /**
     * @return BelongsTo<AcademicTermStructure, $this>
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(AcademicTermStructure::class, 'academic_term_structure_id');
    }

    /**
     * @return BelongsTo<AcademicPeriod, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<AcademicPeriod, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->ordered();
    }

    /**
     * @return HasMany<CurriculumSubject, $this>
     */
    public function curriculumSubjects(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class);
    }

    /**
     * @param  Builder<AcademicPeriod>  $query
     * @return Builder<AcademicPeriod>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', AcademicStatus::Active->value);
    }

    /**
     * @param  Builder<AcademicPeriod>  $query
     * @return Builder<AcademicPeriod>
     */
    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * @param  Builder<AcademicPeriod>  $query
     * @return Builder<AcademicPeriod>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sequence')->orderBy('id');
    }

    /**
     * @return array<string, class-string|string>
     */
    protected function casts(): array
    {
        return [
            'status' => AcademicStatus::class,
            'sequence' => 'integer',
        ];
    }
}

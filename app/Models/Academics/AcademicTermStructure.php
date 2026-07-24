<?php

namespace App\Models\Academics;

use App\Enums\AcademicStatus;
use App\Enums\AcademicTermStructureType;
use Database\Factories\Academics\AcademicTermStructureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property AcademicTermStructureType $type
 * @property AcademicStatus $status
 */
class AcademicTermStructure extends Model
{
    /** @use HasFactory<AcademicTermStructureFactory> */
    use HasFactory;

    protected $attributes = [
        'type' => AcademicTermStructureType::Semester->value,
        'status' => AcademicStatus::Active->value,
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'status',
    ];

    /**
     * @return HasMany<AcademicPeriod, $this>
     */
    public function periods(): HasMany
    {
        return $this->hasMany(AcademicPeriod::class);
    }

    /**
     * @return HasMany<AcademicPeriod, $this>
     */
    public function rootPeriods(): HasMany
    {
        return $this->periods()->roots()->ordered();
    }

    /**
     * @return array<string, class-string>
     */
    protected function casts(): array
    {
        return [
            'type' => AcademicTermStructureType::class,
            'status' => AcademicStatus::class,
        ];
    }
}

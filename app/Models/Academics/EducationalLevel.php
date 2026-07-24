<?php

namespace App\Models\Academics;

use Database\Factories\Academics\EducationalLevelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(
    'name'
)]
class EducationalLevel extends Model
{
    /** @use HasFactory<EducationalLevelFactory> */
    use HasFactory;

    public function gradeLevels(): HasMany
    {
        return $this->hasMany(GradeLevel::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function academicTermStructures(): HasMany
    {
        return $this->hasMany(AcademicTermStructure::class);
    }
}

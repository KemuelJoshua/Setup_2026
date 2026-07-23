<?php

namespace App\Models\Academics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curriculum extends Model
{
    protected $fillable = [
        'code',
        'name',
        'effective_year',
        'description',
        'status',
    ];

    /**
     * @return HasMany<CurriculumSubject, $this>
     */
    public function curriculumSubjects(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class)->orderBy('sort_order');
    }
}

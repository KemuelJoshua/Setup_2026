<?php

namespace App\Models\Academics;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(
    'educational_level_id',
    'name',
)]
class Subject extends Model
{
    public function educationalLevel(): BelongsTo
    {
        return $this->belongsTo(EducationalLevel::class);
    }
}
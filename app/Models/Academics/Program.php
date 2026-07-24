<?php

namespace App\Models\Academics;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(
    'educational_level_id',
    'code',
    'name',
    'description',
    'status',
)]
class Program extends Model
{
    public function educationalLevel(): BelongsTo
    {
        return $this->belongsTo(EducationalLevel::class);
    }
}
<?php

namespace App\Models\Academics;

use App\Enums\SchoolYearStatus;
use Database\Factories\SchoolYearFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(SchoolYearFactory::class)]
#[Fillable(
    'sc_name',
    'sc_code',
    'sc_start_date',
    'sc_end_date',
    'sc_status',
)]
class SchoolYear extends Model
{
    use HasFactory;

    protected $table = 'school_years';

    protected function casts(): array
    {
        return [
            'sc_start_date' => 'date',
            'sc_end_date' => 'date',
            'sc_status' => SchoolYearStatus::class,
        ];
    }
}
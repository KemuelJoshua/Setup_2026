<?php

namespace App\Models;

use App\Enums\SchoolYearStatus;
use Database\Factories\SchoolYearFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    /** @use HasFactory<SchoolYearFactory> */
    use HasFactory;

    protected $table = 'school_years';

    protected $fillable = [
        'sc_name',
        'sc_code',
        'sc_start_date',
        'sc_end_date',
        'sc_status',
    ];

    protected $casts = [
        'sc_start_date' => 'date',
        'sc_end_date' => 'date',
        'sc_status' => SchoolYearStatus::class,
    ];
}

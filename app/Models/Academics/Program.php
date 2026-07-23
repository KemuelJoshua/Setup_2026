<?php

namespace App\Models\Academics;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
    ];
}

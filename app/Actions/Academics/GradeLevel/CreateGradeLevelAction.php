<?php

namespace App\Actions\Academics\GradeLevel;

use App\Models\Academics\GradeLevel;

class CreateGradeLevelAction
{
    public function execute(array $data): GradeLevel
    {
        return GradeLevel::query()->create($data);
    }
}

<?php

namespace App\Actions\Academics\EducationalLevel;

use App\Models\Academics\EducationalLevel;

class CreateEducationalLevelAction
{
    public function execute(array $data): EducationalLevel
    {
        return EducationalLevel::query()->create($data);
    }
}

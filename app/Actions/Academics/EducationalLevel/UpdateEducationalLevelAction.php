<?php

namespace App\Actions\Academics\EducationalLevel;

use App\Models\Academics\EducationalLevel;

class UpdateEducationalLevelAction
{
    public function execute(EducationalLevel $educationalLevel, array $data): EducationalLevel
    {
        $educationalLevel->update($data);

        return $educationalLevel->refresh();
    }
}

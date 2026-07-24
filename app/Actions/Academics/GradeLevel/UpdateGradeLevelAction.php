<?php

namespace App\Actions\Academics\GradeLevel;

use App\Models\Academics\GradeLevel;

class UpdateGradeLevelAction
{
    public function execute(GradeLevel $gradeLevel, array $data): GradeLevel
    {
        $gradeLevel->update($data);

        return $gradeLevel->refresh();
    }
}

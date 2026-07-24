<?php

namespace App\Actions\Academics\GradeLevel;

use App\Models\Academics\GradeLevel;

class DeleteGradeLevelAction
{
    public function execute(GradeLevel $gradeLevel): void
    {
        $gradeLevel->delete();
    }
}

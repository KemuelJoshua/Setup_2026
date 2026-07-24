<?php

namespace App\Actions\Academics\Curriculum;

use App\Models\Academics\Curriculum;

class DeleteCurriculumAction
{
    public function execute(Curriculum $curriculum): void
    {
        $curriculum->delete();
    }
}

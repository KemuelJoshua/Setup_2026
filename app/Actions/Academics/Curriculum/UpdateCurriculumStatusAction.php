<?php

namespace App\Actions\Academics\Curriculum;

use App\Models\Academics\Curriculum;

class UpdateCurriculumStatusAction
{
    public function execute(Curriculum $curriculum, string $status): Curriculum
    {
        $curriculum->update(['status' => $status]);

        return $curriculum;
    }
}

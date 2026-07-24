<?php

namespace App\Actions\Academics\Subject;

use App\Models\Academics\Subject;

class DeleteSubjectAction
{
    public function execute(Subject $subject): void
    {
        $subject->delete();
    }
}

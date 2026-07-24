<?php

namespace App\Actions\Academics\Subject;

use App\Models\Academics\Subject;

class UpdateSubjectAction
{
    public function execute(Subject $subject, array $data): Subject
    {
        $subject->update($data);

        return $subject->refresh();
    }
}

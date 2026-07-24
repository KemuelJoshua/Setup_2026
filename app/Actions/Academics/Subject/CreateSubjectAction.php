<?php

namespace App\Actions\Academics\Subject;

use App\Models\Academics\Subject;

class CreateSubjectAction
{
    public function execute(array $data): Subject
    {
        return Subject::query()->create($data);
    }
}

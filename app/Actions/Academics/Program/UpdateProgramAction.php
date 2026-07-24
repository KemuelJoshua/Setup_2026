<?php

namespace App\Actions\Academics\Program;

use App\Models\Academics\Program;

class UpdateProgramAction
{
    public function execute(Program $program, array $data): Program
    {
        $program->update($data);

        return $program->refresh();
    }
}

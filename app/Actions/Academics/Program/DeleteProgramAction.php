<?php

namespace App\Actions\Academics\Program;

use App\Models\Academics\Program;

class DeleteProgramAction
{
    public function execute(Program $program): void
    {
        $program->delete();
    }
}

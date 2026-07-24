<?php

namespace App\Actions\Academics\Program;

use App\Models\Academics\Program;

class CreateProgramAction
{
    public function execute(array $data): Program
    {
        return Program::query()->create($data);
    }
}

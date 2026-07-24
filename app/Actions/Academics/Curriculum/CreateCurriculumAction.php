<?php

namespace App\Actions\Academics\Curriculum;

use App\Models\Academics\Curriculum;
use Illuminate\Support\Facades\DB;

class CreateCurriculumAction
{
    public function execute(array $data): Curriculum
    {
        return DB::transaction(function () use ($data): Curriculum {
            return Curriculum::query()->create($data);
        });
    }
}

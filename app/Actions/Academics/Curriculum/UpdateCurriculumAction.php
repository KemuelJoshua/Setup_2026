<?php

namespace App\Actions\Academics\Curriculum;

use App\Models\Academics\Curriculum;
use Illuminate\Support\Facades\DB;

class UpdateCurriculumAction
{
    public function execute(Curriculum $curriculum, array $data): Curriculum
    {
        return DB::transaction(function () use ($curriculum, $data): Curriculum {
            unset($data['academic_term_structure_id']);

            $curriculum->update($data);

            return $curriculum->refresh();
        });
    }
}

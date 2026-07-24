<?php

namespace App\Actions\Academics\Curriculum;

use App\Models\Academics\Curriculum;
use Illuminate\Support\Facades\DB;

class UpdateCurriculumAction
{
    public function execute(Curriculum $curriculum, array $data): Curriculum
    {
        return DB::transaction(function () use ($curriculum, $data): Curriculum {
            $curriculumSubjects = $data['curriculum_subjects'] ?? [];
            unset($data['curriculum_subjects']);

            $curriculum->update($data);
            $curriculum->curriculumSubjects()->delete();
            $curriculum->curriculumSubjects()->createMany($curriculumSubjects);

            return $curriculum->refresh();
        });
    }
}

<?php

namespace App\Actions\Academics\Curriculum;

use App\Models\Academics\Curriculum;
use Illuminate\Support\Facades\DB;

class CreateCurriculumAction
{
    public function execute(array $data): Curriculum
    {
        return DB::transaction(function () use ($data): Curriculum {
            $curriculumSubjects = $data['curriculum_subjects'] ?? [];
            unset($data['curriculum_subjects']);

            $curriculum = Curriculum::query()->create($data);
            $curriculum->curriculumSubjects()->createMany($curriculumSubjects);

            return $curriculum;
        });
    }
}

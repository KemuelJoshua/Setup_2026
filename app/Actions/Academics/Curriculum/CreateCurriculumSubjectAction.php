<?php

namespace App\Actions\Academics\Curriculum;

use App\Models\Academics\Curriculum;
use App\Models\Academics\CurriculumSubject;
use Illuminate\Support\Facades\DB;

class CreateCurriculumSubjectAction
{
    public function execute(Curriculum $curriculum, array $data): CurriculumSubject
    {
        return DB::transaction(function () use ($curriculum, $data): CurriculumSubject {
            $prerequisiteIds = $data['prerequisite_ids'] ?? [];
            $corequisiteIds = $data['corequisite_ids'] ?? [];
            unset($data['prerequisite_ids'], $data['corequisite_ids']);

            $curriculumSubject = $curriculum->curriculumSubjects()->create([
                ...$data,
                'is_required' => true,
            ]);
            $curriculumSubject->prerequisites()->sync($prerequisiteIds);
            $curriculumSubject->corequisites()->sync($corequisiteIds);

            return $curriculumSubject;
        });
    }
}

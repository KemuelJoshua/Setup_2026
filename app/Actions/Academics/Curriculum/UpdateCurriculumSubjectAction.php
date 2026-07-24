<?php

namespace App\Actions\Academics\Curriculum;

use App\Models\Academics\CurriculumSubject;
use Illuminate\Support\Facades\DB;

class UpdateCurriculumSubjectAction
{
    public function execute(CurriculumSubject $curriculumSubject, array $data): CurriculumSubject
    {
        return DB::transaction(function () use ($curriculumSubject, $data): CurriculumSubject {
            $prerequisiteIds = $data['prerequisite_ids'] ?? [];
            $corequisiteIds = $data['corequisite_ids'] ?? [];
            unset($data['prerequisite_ids'], $data['corequisite_ids']);

            $curriculumSubject->update($data);
            $curriculumSubject->prerequisites()->sync($prerequisiteIds);
            $curriculumSubject->corequisites()->sync($corequisiteIds);

            return $curriculumSubject->refresh();
        });
    }
}

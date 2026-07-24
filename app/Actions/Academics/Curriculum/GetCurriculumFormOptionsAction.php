<?php

namespace App\Actions\Academics\Curriculum;

use App\Models\Academics\AcademicTerm;
use App\Models\Academics\GradeLevel;
use App\Models\Academics\Subject;
use Illuminate\Database\Eloquent\Collection;

class GetCurriculumFormOptionsAction
{
    /**
     * @return array{
     *     subjects: Collection<int, Subject>,
     *     yearLevels: Collection<int, GradeLevel>,
     *     academicTerms: Collection<int, AcademicTerm>
     * }
     */
    public function execute(): array
    {
        return [
            'subjects' => Subject::query()->orderBy('name')->get(['id', 'name']),
            'yearLevels' => GradeLevel::query()->orderBy('name')->get(['id', 'name']),
            'academicTerms' => AcademicTerm::query()
                ->orderBy('type')
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'type']),
        ];
    }
}

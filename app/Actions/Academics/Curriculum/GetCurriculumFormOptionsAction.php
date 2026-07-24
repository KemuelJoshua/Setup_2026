<?php

namespace App\Actions\Academics\Curriculum;

use App\Models\Academics\AcademicPeriod;
use App\Models\Academics\GradeLevel;
use App\Models\Academics\Subject;
use Illuminate\Database\Eloquent\Collection;

class GetCurriculumFormOptionsAction
{
    /**
     * @return array{
     *     subjects: Collection<int, Subject>,
     *     yearLevels: Collection<int, GradeLevel>,
     *     academicPeriods: Collection<int, AcademicPeriod>
     * }
     */
    public function execute(): array
    {
        return [
            'subjects' => Subject::query()->orderBy('name')->get(['id', 'name']),
            'yearLevels' => GradeLevel::query()->orderBy('name')->get(['id', 'name']),
            'academicPeriods' => AcademicPeriod::query()
                ->roots()
                ->with('structure:id,name,type')
                ->active()
                ->ordered()
                ->get([
                    'id',
                    'academic_term_structure_id',
                    'name',
                    'code',
                ]),
        ];
    }
}

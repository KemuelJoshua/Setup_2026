<?php

namespace App\Actions\Academics\Curriculum;

use App\Models\Academics\AcademicTermStructure;
use App\Models\Academics\GradeLevel;
use App\Models\Academics\Program;
use App\Models\Academics\Subject;
use Illuminate\Database\Eloquent\Collection;

class GetCurriculumFormOptionsAction
{
    /**
     * @return array{
     *     programs: Collection<int, Program>,
     *     academicStructures: Collection<int, AcademicTermStructure>,
     *     yearLevels: Collection<int, GradeLevel>,
     *     subjects: Collection<int, Subject>
     * }
     */
    public function execute(): array
    {
        return [
            'programs' => Program::query()
                ->where('status', 'Active')
                ->orderBy('name')
                ->get(['id', 'code', 'name']),
            'academicStructures' => AcademicTermStructure::query()
                ->active()
                ->with([
                    'rootPeriods' => fn ($query) => $query->active()
                        ->select([
                            'id',
                            'academic_term_structure_id',
                            'name',
                            'code',
                            'sequence',
                        ]),
                ])
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'type']),
            'subjects' => Subject::query()->orderBy('name')->get(['id', 'name']),
            'yearLevels' => GradeLevel::query()->orderBy('name')->get(['id', 'name']),
        ];
    }
}

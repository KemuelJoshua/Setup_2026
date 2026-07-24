<?php

namespace App\Actions\Academics\AcademicTerm;

use App\Models\Academics\AcademicTerm;
use Illuminate\Support\Facades\DB;

class CreateAcademicTermAction
{
    /**
     * @param  array{name: string, code: string, type: string, grading_periods?: array<int, array{name: string, code: string, sort_order: int}>}  $data
     */
    public function execute(array $data): AcademicTerm
    {
        return DB::transaction(function () use ($data): AcademicTerm {
            $gradingPeriods = $data['grading_periods'] ?? [];
            unset($data['grading_periods']);

            $academicTerm = AcademicTerm::query()->create($data);
            $academicTerm->gradingPeriods()->createMany($gradingPeriods);

            return $academicTerm;
        });
    }
}

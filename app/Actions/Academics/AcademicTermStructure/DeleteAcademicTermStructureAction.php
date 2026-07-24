<?php

namespace App\Actions\Academics\AcademicTermStructure;

use App\Models\Academics\AcademicTermStructure;
use Illuminate\Validation\ValidationException;

class DeleteAcademicTermStructureAction
{
    public function execute(AcademicTermStructure $academicTermStructure): void
    {
        if ($academicTermStructure->periods()->whereHas('curriculumSubjects')->exists()) {
            throw ValidationException::withMessages([
                'academic_term_structure' => 'This structure has periods assigned to a curriculum.',
            ]);
        }

        $academicTermStructure->delete();
    }
}

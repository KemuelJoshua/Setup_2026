<?php

namespace App\Actions\Academics\AcademicTerm;

use App\Models\Academics\AcademicTerm;
use Illuminate\Validation\ValidationException;

class DeleteAcademicTermAction
{
    public function execute(AcademicTerm $academicTerm): void
    {
        if ($academicTerm->curriculumSubjects()->exists()) {
            throw ValidationException::withMessages([
                'academic_term' => 'This academic term is assigned to a curriculum.',
            ]);
        }

        $academicTerm->delete();
    }
}

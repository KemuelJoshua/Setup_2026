<?php

namespace App\Actions\Academics\AcademicPeriod;

use App\Models\Academics\AcademicPeriod;
use Illuminate\Validation\ValidationException;

class DeleteAcademicPeriodAction
{
    public function execute(AcademicPeriod $academicPeriod): void
    {
        if ($academicPeriod->curriculumSubjects()->exists()) {
            throw ValidationException::withMessages([
                'academic_period' => 'This academic period is assigned to a curriculum.',
            ]);
        }

        $academicPeriod->delete();
    }
}

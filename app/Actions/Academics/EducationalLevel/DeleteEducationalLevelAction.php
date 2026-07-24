<?php

namespace App\Actions\Academics\EducationalLevel;

use App\Models\Academics\EducationalLevel;
use Illuminate\Validation\ValidationException;

class DeleteEducationalLevelAction
{
    /**
     * @throws ValidationException
     */
    public function execute(EducationalLevel $educationalLevel): void
    {
        $isInUse = $educationalLevel->gradeLevels()->exists()
            || $educationalLevel->sections()->exists()
            || $educationalLevel->subjects()->exists()
            || $educationalLevel->programs()->exists();

        if ($isInUse) {
            throw ValidationException::withMessages([
                'educational_level' => 'This educational level is in use and cannot be deleted.',
            ]);
        }

        $educationalLevel->delete();
    }
}

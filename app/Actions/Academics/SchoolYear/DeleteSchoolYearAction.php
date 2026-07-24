<?php

namespace App\Actions\Academics\SchoolYear;

use App\Models\Academics\SchoolYear;

class DeleteSchoolYearAction
{
    public function execute(string $id): SchoolYear
    {
        $schoolYear = SchoolYear::findOrFail($id);

        $schoolYear->delete();

        return $schoolYear;
    }
}

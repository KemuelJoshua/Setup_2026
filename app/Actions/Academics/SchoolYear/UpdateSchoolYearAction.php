<?php

namespace App\Actions\Academics\SchoolYear;

use App\Models\SchoolYear;

class UpdateSchoolYearAction
{
    public function execute(string $id, array $data): SchoolYear
    {
        $schoolYear = SchoolYear::findOrFail($id);

        $schoolYear->update($data);

        return $schoolYear->refresh();
    }
}

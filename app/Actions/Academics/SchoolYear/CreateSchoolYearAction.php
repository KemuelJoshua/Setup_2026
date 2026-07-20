<?php

namespace App\Actions\Academics\SchoolYear;

use App\Models\SchoolYear;

class CreateSchoolYearAction
{
    public function execute(array $data): SchoolYear
    {
        return SchoolYear::create($data);
    }
}

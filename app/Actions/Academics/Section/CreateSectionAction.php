<?php

namespace App\Actions\Academics\Section;

use App\Models\Academics\Section;

class CreateSectionAction
{
    public function execute(array $data): Section
    {
        return Section::query()->create($data);
    }
}

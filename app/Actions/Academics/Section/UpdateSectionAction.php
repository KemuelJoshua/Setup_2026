<?php

namespace App\Actions\Academics\Section;

use App\Models\Academics\Section;

class UpdateSectionAction
{
    public function execute(Section $section, array $data): Section
    {
        $section->update($data);

        return $section->refresh();
    }
}

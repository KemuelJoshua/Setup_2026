<?php

namespace App\Actions\Academics\Section;

use App\Models\Academics\Section;

class DeleteSectionAction
{
    public function execute(Section $section): void
    {
        $section->delete();
    }
}

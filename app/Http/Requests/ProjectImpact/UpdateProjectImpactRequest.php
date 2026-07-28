<?php

namespace App\Http\Requests\ProjectImpact;

use App\Models\ProjectImpact;

class UpdateProjectImpactRequest extends StoreProjectImpactRequest
{
    public function authorize(): bool
    {
        $projectImpact = $this->route('project_impact');

        return $projectImpact instanceof ProjectImpact
            && $this->user()?->can('update', $projectImpact) === true;
    }
}

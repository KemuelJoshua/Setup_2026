<?php

namespace App\Policies;

use App\Models\ProjectImpact;
use App\Models\User;

class ProjectImpactPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ProjectImpact $projectImpact): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ProjectImpact $projectImpact): bool
    {
        return true;
    }

    public function delete(User $user, ProjectImpact $projectImpact): bool
    {
        return true;
    }

    public function import(User $user): bool
    {
        return true;
    }

    public function restore(User $user, ProjectImpact $projectImpact): bool
    {
        return false;
    }

    public function forceDelete(User $user, ProjectImpact $projectImpact): bool
    {
        return false;
    }
}

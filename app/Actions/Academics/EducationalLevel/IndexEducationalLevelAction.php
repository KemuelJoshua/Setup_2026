<?php

namespace App\Actions\Academics\EducationalLevel;

use App\Models\Academics\EducationalLevel;
use Illuminate\Database\Eloquent\Builder;

class IndexEducationalLevelAction
{
    /**
     * @param  array{search?: string|null}  $filters
     * @return Builder<EducationalLevel>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return EducationalLevel::query()
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->latest();
    }
}

<?php

namespace App\Actions\Academics\GradeLevel;

use App\Models\Academics\GradeLevel;
use Illuminate\Database\Eloquent\Builder;

class IndexGradeLevelAction
{
    /**
     * @param  array{search?: string|null}  $filters
     * @return Builder<GradeLevel>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return GradeLevel::query()
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->latest();
    }
}

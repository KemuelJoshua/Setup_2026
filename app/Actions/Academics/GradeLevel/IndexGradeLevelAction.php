<?php

namespace App\Actions\Academics\GradeLevel;

use App\Models\Academics\GradeLevel;
use Illuminate\Database\Eloquent\Builder;

class IndexGradeLevelAction
{
    /**
     * @param  array{search?: string|null, educational_level_id?: int|null}  $filters
     * @return Builder<GradeLevel>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $educationalLevelId = $filters['educational_level_id'] ?? null;

        return GradeLevel::query()
            ->with('educationalLevel:id,name')
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->when(
                $educationalLevelId,
                fn (Builder $query) => $query->where('educational_level_id', $educationalLevelId),
            )
            ->latest();
    }
}

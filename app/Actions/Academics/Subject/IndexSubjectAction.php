<?php

namespace App\Actions\Academics\Subject;

use App\Models\Academics\Subject;
use Illuminate\Database\Eloquent\Builder;

class IndexSubjectAction
{
    /**
     * @param  array{search?: string|null, educational_level_id?: int|null}  $filters
     * @return Builder<Subject>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $educationalLevelId = $filters['educational_level_id'] ?? null;

        return Subject::query()
            ->with('educationalLevel:id,name')
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->when(
                $educationalLevelId,
                fn (Builder $query) => $query->where('educational_level_id', $educationalLevelId),
            )
            ->latest();
    }
}

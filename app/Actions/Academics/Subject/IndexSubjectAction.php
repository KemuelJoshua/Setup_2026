<?php

namespace App\Actions\Academics\Subject;

use App\Models\Academics\Subject;
use Illuminate\Database\Eloquent\Builder;

class IndexSubjectAction
{
    /**
     * @param  array{search?: string|null}  $filters
     * @return Builder<Subject>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return Subject::query()
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->latest();
    }
}

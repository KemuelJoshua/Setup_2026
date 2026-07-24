<?php

namespace App\Actions\Academics\Section;

use App\Models\Academics\Section;
use Illuminate\Database\Eloquent\Builder;

class IndexSectionAction
{
    /**
     * @param  array{search?: string|null}  $filters
     * @return Builder<Section>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return Section::query()
            ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->latest();
    }
}

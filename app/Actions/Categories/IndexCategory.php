<?php

namespace App\Actions\Categories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

class IndexCategory
{
    /** @param array{search?: string|null} $filters
     * @return Builder<Category>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return Category::query()
            ->withCount('tenants')
            ->when(
                $search !== '',
                fn (Builder $query) => $query->where('name', 'like', "%{$search}%"),
            )
            ->latest();
    }
}

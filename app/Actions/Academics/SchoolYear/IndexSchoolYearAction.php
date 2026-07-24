<?php

namespace App\Actions\Academics\SchoolYear;

use App\Models\Academics\SchoolYear;
use Illuminate\Database\Eloquent\Builder;

class IndexSchoolYearAction
{
    /**
     * @return Builder<int, SchoolYear>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return SchoolYear::query()
            ->when(
                $search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query
                        ->where('sc_name', 'like', "%{$search}%")
                        ->orWhere('sc_code', 'like', "%{$search}%")
                        ->orWhere('sc_status', 'like', "%{$search}%")
                )
            )
            ->latest();
    }
}

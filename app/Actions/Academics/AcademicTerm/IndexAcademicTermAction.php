<?php

namespace App\Actions\Academics\AcademicTerm;

use App\Models\Academics\AcademicTerm;
use Illuminate\Database\Eloquent\Builder;

class IndexAcademicTermAction
{
    /**
     * @param  array{search?: string|null}  $filters
     * @return Builder<AcademicTerm>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return AcademicTerm::query()
            ->with('gradingPeriods:id,academic_term_id,name,code,sort_order')
            ->when(
                $search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                )
            )
            ->orderBy('id');
    }
}

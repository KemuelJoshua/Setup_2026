<?php

namespace App\Actions\Academics\AcademicTermStructure;

use App\Models\Academics\AcademicTermStructure;
use Illuminate\Database\Eloquent\Builder;

class IndexAcademicTermStructureAction
{
    /**
     * @param  array{search?: string|null}  $filters
     * @return Builder<AcademicTermStructure>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return AcademicTermStructure::query()
            ->select(['id', 'name', 'code', 'type', 'status', 'created_at', 'updated_at'])
            ->with([
                'rootPeriods:id,academic_term_structure_id,parent_id,name,code,sequence,status',
                'rootPeriods.children:id,academic_term_structure_id,parent_id,name,code,sequence,status',
            ])
            ->when(
                $search !== '',
                fn (Builder $query): Builder => $query->where(
                    fn (Builder $query): Builder => $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%"),
                ),
            )
            ->latest();
    }
}

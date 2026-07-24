<?php

namespace App\Actions\Academics\AcademicTermStructure;

use App\Models\Academics\AcademicTermStructure;
use Illuminate\Database\Eloquent\Builder;

class IndexAcademicTermStructureAction
{
    /**
     * @param  array{search?: string|null, educational_level_id?: int|null}  $filters
     * @return Builder<AcademicTermStructure>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $educationalLevelId = $filters['educational_level_id'] ?? null;

        return AcademicTermStructure::query()
            ->select([
                'id',
                'educational_level_id',
                'name',
                'code',
                'type',
                'status',
                'created_at',
                'updated_at',
            ])
            ->with([
                'educationalLevel:id,name',
                'rootPeriods:id,academic_term_structure_id,parent_id,name,code,sequence,status',
                'rootPeriods.children:id,academic_term_structure_id,parent_id,name,code,sequence,status',
            ])
            ->when(
                $educationalLevelId !== null,
                fn (Builder $query): Builder => $query->where(
                    'educational_level_id',
                    $educationalLevelId,
                ),
            )
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

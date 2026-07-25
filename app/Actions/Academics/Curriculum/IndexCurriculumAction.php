<?php

namespace App\Actions\Academics\Curriculum;

use App\Models\Academics\Curriculum;
use Illuminate\Database\Eloquent\Builder;

class IndexCurriculumAction
{
    /**
     * @param  array{
     *     search?: string|null,
     *     educational_level_id?: int|null,
     *     program_id?: int|null,
     *     status?: string|null
     * }  $filters
     * @return Builder<Curriculum>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $educationalLevelId = $filters['educational_level_id'] ?? null;
        $programId = $filters['program_id'] ?? null;
        $status = $filters['status'] ?? null;

        return Curriculum::query()
            ->with([
                'program:id,educational_level_id,name',
                'academicTermStructure:id,name',
            ])
            ->withCount('curriculumSubjects')
            ->when(
                $search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query
                        ->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('effective_year', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('program', fn (Builder $query) => $query
                            ->where('name', 'like', "%{$search}%"))
                )
            )
            ->when(
                $educationalLevelId,
                fn (Builder $query) => $query->whereHas(
                    'program',
                    fn (Builder $query) => $query->where(
                        'educational_level_id',
                        $educationalLevelId,
                    ),
                ),
            )
            ->when(
                $programId,
                fn (Builder $query) => $query->where('program_id', $programId),
            )
            ->when(
                $status,
                fn (Builder $query) => $query->where('status', $status),
            )
            ->latest();
    }
}

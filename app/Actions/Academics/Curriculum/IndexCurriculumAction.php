<?php

namespace App\Actions\Academics\Curriculum;

use App\Models\Academics\Curriculum;
use Illuminate\Database\Eloquent\Builder;

class IndexCurriculumAction
{
    /**
     * @param  array{search?: string|null}  $filters
     * @return Builder<Curriculum>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return Curriculum::query()
            ->with([
                'program:id,name',
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
            ->latest();
    }
}

<?php

namespace App\Actions\Academics\Program;

use App\Models\Academics\Program;
use Illuminate\Database\Eloquent\Builder;

class IndexProgramAction
{
    /**
     * @param  array{search?: string|null, educational_level_id?: int|null}  $filters
     * @return Builder<Program>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $educationalLevelId = $filters['educational_level_id'] ?? null;

        return Program::query()
            ->with('educationalLevel:id,name')
            ->when(
                $search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query
                        ->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                )
            )
            ->when(
                $educationalLevelId,
                fn (Builder $query) => $query->where('educational_level_id', $educationalLevelId),
            )
            ->latest();
    }
}

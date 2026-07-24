<?php

namespace App\Actions\Academics\Program;

use App\Models\Academics\Program;
use Illuminate\Database\Eloquent\Builder;

class IndexProgramAction
{
    /**
     * @param  array{search?: string|null}  $filters
     * @return Builder<Program>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return Program::query()
            ->when(
                $search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query
                        ->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                )
            )
            ->latest();
    }
}

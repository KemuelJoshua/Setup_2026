<?php

namespace App\Actions\Roles;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class IndexRole
{
    public function execute($filters = []): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $guard = trim((string) ($filters['guard'] ?? 'all'));

        return Role::query()
            ->select(['id', 'name', 'guard_name'])
            ->with([
                'permissions' => fn ($query) => $query
                    ->select('id', 'name')
                    ->where('name', 'like', '%admin%'),
            ])
            ->when($search !== '', fn (Builder $query) => $query
                ->where(fn (Builder $query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('guard_name', 'like', "%{$search}%")))
            ->when($guard !== 'all', fn (Builder $query) => $query->where('guard_name', $guard))
            ->whereNotIn('name', [
                'School Admin',
                'Teacher',
                'Student',
            ])
            ->orderBy('name');
    }
}

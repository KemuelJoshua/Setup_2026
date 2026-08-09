<?php

namespace App\Actions\Tenants;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;

class IndexTenant
{
    /** @param array{search?: string|null} $filters
     * @return Builder<Tenant>
     */
    public function execute(array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return Tenant::query()
            ->with([
                'category:id,name',
                'domains:id,domain,tenant_id',
            ])
            ->when(
                $search !== '',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query
                        ->where('school_name', 'like', "%{$search}%")
                        ->orWhere('school_code', 'like', "%{$search}%")
                        ->orWhere('school_email', 'like', "%{$search}%")
                        ->orWhereHas('domains', fn (Builder $query) => $query->where('domain', 'like', "%{$search}%"))
                )
            )
            ->latest();
    }
}

<?php

namespace App\Actions\Tenants;

use App\Models\Tenant;

class UpdateTenantStatus
{
    public function execute(Tenant $tenant, bool $isActive): Tenant
    {
        $tenant->update(['is_active' => $isActive]);

        return $tenant->refresh();
    }
}

<?php

namespace App\Actions\Tenants;

use App\Models\Tenant;
use Illuminate\Validation\ValidationException;

class DeleteTenant
{
    public function execute(Tenant $tenant): void
    {
        if ((bool) $tenant->is_active) {
            throw ValidationException::withMessages([
                'tenant' => 'Deactivate the school before deleting it.',
            ]);
        }

        $tenant->delete();
    }
}

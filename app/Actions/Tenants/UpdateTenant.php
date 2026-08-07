<?php

namespace App\Actions\Tenants;

use App\Models\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UpdateTenant
{
    /** @param array<string, mixed> $data */
    public function execute(Tenant $tenant, array $data): Tenant
    {
        DB::connection(config('tenancy.database.central_connection'))->transaction(function () use ($data, $tenant): void {
            $tenant->update(Arr::except($data, ['domain']));
            $tenant->domains()->firstOrFail()->update(['domain' => mb_strtolower($data['domain'])]);
        });

        return $tenant->refresh()->load('domains');
    }
}

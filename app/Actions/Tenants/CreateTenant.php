<?php

namespace App\Actions\Tenants;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Arr;
use Throwable;

class CreateTenant
{
    /** @param array<string, mixed> $data */
    public function execute(array $data): Tenant
    {
        $tenant = new Tenant;

        try {
            $tenant->fill(Arr::except($data, [
                'domain', 'admin_name', 'admin_email', 'admin_password',
            ]));
            $tenant->save();

            $tenant->domains()->create(['domain' => mb_strtolower($data['domain'])]);

            $tenant->run(function () use ($data): void {
                $administrator = User::query()->create([
                    'name' => $data['admin_name'],
                    'email' => $data['admin_email'],
                    'password' => $data['admin_password'],
                ]);

                $administrator->forceFill(['email_verified_at' => now()])->save();

                $administrator->assignRole('School Admin');
            });

            return $tenant->load('domains');
        } catch (Throwable $exception) {
            if ($tenant->exists) {
                $tenant->delete();
            }

            throw $exception;
        }
    }
}

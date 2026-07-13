<?php

namespace App\Actions\Roles;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UpdateRole
{
    public function execute(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            $role->update([
                'name' => $data['name'],
            ]);

            $this->assignPermissions($role, $data['permissions'] ?? []);

            return $role;
        });
    }

    private function assignPermissions(Role $role, array $permissions): void
    {
        $permissionIds = array_map(
            static fn (int|string $permission): int => (int) $permission,
            $permissions,
        );

        $role->syncPermissions($permissionIds);
    }
}

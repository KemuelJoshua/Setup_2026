<?php

namespace App\Actions\Roles;

use App\Models\User;
use App\Notifications\RoleCreatedNotification;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CreateRole
{
    public function execute(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web',
            ]);

            $this->assignPermissions($role, $data['permissions'] ?? []);

            // Notify all Superadmins
            User::role('Superadmin')
                ->get()
                ->each(function (User $user) use ($role) {
                    $user->notify(new RoleCreatedNotification($role));
                });

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

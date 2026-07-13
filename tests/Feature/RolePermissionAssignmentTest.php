<?php

use App\Actions\Roles\UpdateRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('role permissions can be updated from submitted string ids', function () {
    $originalPermission = Permission::create([
        'name' => 'view courses',
        'guard_name' => 'web',
    ]);
    $replacementPermission = Permission::create([
        'name' => 'update courses',
        'guard_name' => 'web',
    ]);
    $role = Role::create([
        'name' => 'Instructor',
        'guard_name' => 'web',
    ]);
    $role->givePermissionTo($originalPermission);

    $updatedRole = app(UpdateRole::class)->execute($role, [
        'name' => 'Senior Instructor',
        'permissions' => [(string) $replacementPermission->id],
    ]);

    $updatedRole->refresh();

    expect($updatedRole->hasPermissionTo($replacementPermission))->toBeTrue()
        ->and($updatedRole->hasPermissionTo($originalPermission))->toBeFalse();
});

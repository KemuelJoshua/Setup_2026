<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('authenticated users can create a role with permissions', function () {
    $user = User::factory()->create();
    $createRolesPermission = Permission::create([
        'name' => 'create roles',
        'guard_name' => 'web',
    ]);
    $user->givePermissionTo($createRolesPermission);

    $permission = Permission::create([
        'name' => 'manage courses',
        'guard_name' => 'web',
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('administration.roles.store'), [
            'name' => 'Instructor',
            'permissions' => [(string) $permission->getKey()],
        ]);

    $response
        ->assertRedirect(route('administration.roles.index'))
        ->assertSessionHas('success', 'Role created successfully.');

    $role = Role::findByName('Instructor');

    $this->assertModelExists($role);
    expect($role->hasPermissionTo($permission))->toBeTrue();
});

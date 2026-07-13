<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    foreach (['view roles', 'create roles', 'update roles', 'delete roles'] as $permission) {
        Permission::create([
            'name' => $permission,
            'guard_name' => 'web',
        ]);
    }
});

test('authorized users can view the roles page', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('view roles');

    $this
        ->actingAs($user)
        ->get(route('administration.roles.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('RolesAndPermissions/Index')
            ->has('roles.data'));
});

test('authorized users can create a role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('create roles');

    $response = $this
        ->actingAs($user)
        ->post(route('administration.roles.store'), [
            'name' => 'Admin',
            'permissions' => [],
        ]);

    $response
        ->assertRedirect(route('administration.roles.index'))
        ->assertSessionHas('success', 'Role created successfully.');

    expect(Role::findByName('Admin')->guard_name)->toBe('web');
});

test('authorized users can update a role and replace its permissions', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('update roles');
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

    $response = $this
        ->actingAs($user)
        ->put(route('administration.roles.update', $role), [
            'name' => 'Senior Instructor',
            'permissions' => [(string) $replacementPermission->getKey()],
        ]);

    $response
        ->assertRedirect(route('administration.roles.index'))
        ->assertSessionHas('success', 'Role updated successfully.');

    $role->refresh();

    expect($role->name)->toBe('Senior Instructor')
        ->and($role->hasPermissionTo($replacementPermission))->toBeTrue()
        ->and($role->hasPermissionTo($originalPermission))->toBeFalse();
});

test('role update validates duplicate names and permission guards', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('update roles');
    $role = Role::create([
        'name' => 'Instructor',
        'guard_name' => 'web',
    ]);
    Role::create([
        'name' => 'Administrator',
        'guard_name' => 'web',
    ]);
    $apiPermission = Permission::create([
        'name' => 'manage api courses',
        'guard_name' => 'api',
    ]);

    $this
        ->actingAs($user)
        ->from(route('administration.roles.index'))
        ->put(route('administration.roles.update', $role), [
            'name' => 'Administrator',
            'permissions' => [$apiPermission->getKey()],
        ])
        ->assertRedirect(route('administration.roles.index'))
        ->assertSessionHasErrors(['name', 'permissions.0']);

    expect($role->refresh()->name)->toBe('Instructor');
});

test('authorized users can delete a role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('delete roles');
    $role = Role::create([
        'name' => 'Instructor',
        'guard_name' => 'web',
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(route('administration.roles.destroy', $role));

    $response
        ->assertRedirect(route('administration.roles.index'))
        ->assertSessionHas('success', 'Role deleted successfully.');

    $this->assertModelMissing($role);
});

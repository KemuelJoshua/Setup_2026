<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

test('users without permission cannot view roles', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->get(route('admin.settings.roles.index'))
        ->assertForbidden();
});

test('users without permission cannot create roles', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('admin.settings.roles.store'), [
            'name' => 'Instructor',
            'permissions' => [],
        ])
        ->assertForbidden();

    expect(Role::query()->where('name', 'Instructor')->exists())->toBeFalse();
});

test('users without permission cannot update roles', function () {
    $user = User::factory()->create();
    $role = Role::create([
        'name' => 'Instructor',
        'guard_name' => 'web',
    ]);

    $this
        ->actingAs($user)
        ->put(route('admin.settings.roles.update', $role), [
            'name' => 'Senior Instructor',
            'permissions' => [],
        ])
        ->assertForbidden();

    expect($role->refresh()->name)->toBe('Instructor');
});

test('users without permission cannot delete roles', function () {
    $user = User::factory()->create();
    $role = Role::create([
        'name' => 'Instructor',
        'guard_name' => 'web',
    ]);

    $this
        ->actingAs($user)
        ->delete(route('admin.settings.roles.destroy', $role))
        ->assertForbidden();

    $this->assertModelExists($role);
});

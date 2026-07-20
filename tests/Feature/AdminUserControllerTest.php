<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('authenticated users can view the users page', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create([
        'name' => 'Jane Manager',
        'email' => 'jane@example.com',
    ]);

    $role = Role::create([
        'name' => 'Teacher',
        'guard_name' => 'web',
    ]);

    $user->assignRole($role);

    $this
        ->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/users/Index')
            ->has('users.data')
            ->where('users.data.0.name', 'Jane Manager')
            ->where('users.data.0.roles.0', 'Teacher')
            ->has('roles'));
});

test('authenticated users can create a user', function () {
    $admin = User::factory()->create();

    Role::create([
        'name' => 'Teacher',
        'guard_name' => 'web',
    ]);

    $this
        ->actingAs($admin)
        ->post(route('admin.users.store'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_name' => 'Teacher',
        ])
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User created successfully.');

    $user = User::where('email', 'john@example.com')->firstOrFail();

    expect($user->name)->toBe('John Doe')
        ->and(Hash::check('password', $user->password))->toBeTrue()
        ->and($user->hasRole('Teacher'))->toBeTrue();
});

test('authenticated users can update a user', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'password' => 'old-password',
    ]);

    Role::create([
        'name' => 'Student',
        'guard_name' => 'web',
    ]);

    $this
        ->actingAs($admin)
        ->put(route('admin.users.update', $user), [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'password' => '',
            'password_confirmation' => '',
            'role_name' => 'Student',
        ])
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User updated successfully.');

    $user->refresh();

    expect($user->name)->toBe('New Name')
        ->and($user->email)->toBe('new@example.com')
        ->and(Hash::check('old-password', $user->password))->toBeTrue()
        ->and($user->hasRole('Student'))->toBeTrue();
});

test('authenticated users can delete a user', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();

    $this
        ->actingAs($admin)
        ->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success', 'User deleted successfully.');

    $this->assertModelMissing($user);
});

test('user forms validate required fields', function () {
    $admin = User::factory()->create();

    $this
        ->actingAs($admin)
        ->from(route('admin.users.index'))
        ->post(route('admin.users.store'), [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'password',
            'password_confirmation' => 'different',
        ])
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHasErrors(['name', 'email', 'password']);
});

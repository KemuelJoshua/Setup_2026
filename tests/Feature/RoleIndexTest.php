<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('authenticated users can visit the roles index', function () {
    $user = User::factory()->create();
    $viewRolesPermission = Permission::create([
        'name' => 'admin view roles',
        'guard_name' => 'web',
    ]);
    $user->givePermissionTo($viewRolesPermission);

    foreach (range(1, 12) as $number) {
        Role::create([
            'name' => 'Role '.Str::padLeft((string) $number, 2, '0'),
            'guard_name' => 'web',
        ]);
    }

    $permission = Permission::create([
        'name' => 'admin manage courses',
        'guard_name' => 'web',
    ]);
    Role::findByName('Role 01')->givePermissionTo($permission);
    $apiPermission = Permission::create([
        'name' => 'admin manage api courses',
        'guard_name' => 'api',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('admin.settings.roles.index'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/settings/RolesAndPermissions/Index')
            ->has('roles.data', 10)
            ->where('roles.current_page', 1)
            ->where('roles.last_page', 2)
            ->where('roles.total', 12)
            ->where('roles.data.0.name', 'Role 01')
            ->where('roles.data.0.permission_ids.0', $permission->id)
            ->where('roles.data.9.name', 'Role 10')
            ->where('guards.0', 'web')
            ->has('permissions', 3)
            ->where('permissions.0.id', $apiPermission->id)
            ->where('permissions.0.name', 'manage api courses')
            ->where('permissions.0.category', 'Courses')
            ->where('permissions.0.guard_name', 'api')
            ->where('permissions.1.id', $permission->id)
            ->where('permissions.1.name', 'manage courses')
            ->where('permissions.1.category', 'Courses')
            ->where('permissions.1.guard_name', 'web')
            ->where('permissions.2.id', $viewRolesPermission->id)
            ->where('permissions.2.name', 'view roles')
            ->where('permissions.2.category', 'Roles')
            ->where('permissions.2.guard_name', 'web')
            ->where('filters.search', '')
            ->where('filters.guard', 'all'),
        );
});

test('roles can be searched and filtered while paginating', function () {
    $user = User::factory()->create();
    $viewRolesPermission = Permission::create([
        'name' => 'admin view roles',
        'guard_name' => 'web',
    ]);
    $user->givePermissionTo($viewRolesPermission);

    foreach (range(1, 11) as $number) {
        Role::create([
            'name' => 'Instructor '.Str::padLeft((string) $number, 2, '0'),
            'guard_name' => 'web',
        ]);
    }

    Role::create(['name' => 'Student', 'guard_name' => 'web']);
    Role::create(['name' => 'API Instructor', 'guard_name' => 'api']);

    $response = $this
        ->actingAs($user)
        ->get(route('admin.settings.roles.index', [
            'search' => 'Instructor',
            'guard' => 'web',
        ]));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('roles.data', 10)
            ->where('roles.data.0.name', 'Instructor 01')
            ->where('roles.last_page', 2)
            ->where('roles.total', 11)
            ->where('roles.next_page_url', fn (string $url) => Str::containsAll($url, [
                'search=Instructor',
                'guard=web',
                'page=2',
            ]))
            ->where('filters.search', 'Instructor')
            ->where('filters.guard', 'web'),
        );
});

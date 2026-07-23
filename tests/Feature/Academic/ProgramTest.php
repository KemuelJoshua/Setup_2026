<?php

use App\Models\Academics\Program;
use App\Models\User;
use Database\Seeders\permissions\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    foreach ([
        'admin view programs',
        'admin create programs',
        'admin update programs',
        'admin delete programs',
    ] as $permission) {
        Permission::create([
            'name' => $permission,
            'guard_name' => 'web',
        ]);
    }
});

test('authorized users can view and search programs', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin view programs');

    Program::query()->create([
        'code' => 'BSCS',
        'name' => 'Computer Science',
        'description' => 'Computing program',
        'status' => 'Active',
    ]);

    $this
        ->actingAs($user)
        ->get(route('admin.academics.program.index', ['search' => 'BSCS']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/academics/programs/Index')
            ->where('programs.total', 1)
            ->where('programs.data.0.code', 'BSCS')
            ->where('programs.data.0.name', 'Computer Science'));
});

test('authorized users can create a program', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create programs');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.program.store'), [
            'code' => 'BSCS',
            'name' => 'Computer Science',
            'description' => null,
            'status' => 'Active',
        ])
        ->assertRedirect(route('admin.academics.program.index'))
        ->assertSessionHas('success', 'Program created successfully.');

    $program = Program::query()->where('code', 'BSCS')->firstOrFail();

    expect($program->name)->toBe('Computer Science')
        ->and($program->description)->toBeNull()
        ->and($program->created_at)->not->toBeNull()
        ->and($program->updated_at)->not->toBeNull();
});

test('authorized users can update a program', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update programs');
    $program = Program::query()->create([
        'code' => 'BSCS',
        'name' => 'Computer Science',
        'description' => null,
        'status' => 'Active',
    ]);

    $this
        ->actingAs($user)
        ->put(route('admin.academics.program.update', $program), [
            'code' => 'BSCS',
            'name' => 'Bachelor of Science in Computer Science',
            'description' => 'Updated description',
            'status' => 'Inactive',
        ])
        ->assertRedirect(route('admin.academics.program.index'))
        ->assertSessionHas('success', 'Program updated successfully.');

    expect($program->refresh()->name)
        ->toBe('Bachelor of Science in Computer Science')
        ->and($program->description)->toBe('Updated description')
        ->and($program->status)->toBe('Inactive');
});

test('authorized users can delete a program', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin delete programs');
    $program = Program::query()->create([
        'code' => 'BSCS',
        'name' => 'Computer Science',
        'description' => null,
        'status' => 'Active',
    ]);

    $this
        ->actingAs($user)
        ->delete(route('admin.academics.program.destroy', $program))
        ->assertRedirect(route('admin.academics.program.index'))
        ->assertSessionHas('success', 'Program deleted successfully.');

    $this->assertModelMissing($program);
});

test('program forms validate required fields and unique codes', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create programs');

    Program::query()->create([
        'code' => 'BSCS',
        'name' => 'Computer Science',
        'description' => null,
        'status' => 'Active',
    ]);

    $this
        ->actingAs($user)
        ->from(route('admin.academics.program.index'))
        ->post(route('admin.academics.program.store'), [
            'code' => 'BSCS',
            'name' => '',
            'description' => null,
            'status' => '',
        ])
        ->assertRedirect(route('admin.academics.program.index'))
        ->assertSessionHasErrors(['code', 'name', 'status']);
});

test('users without permission cannot create a program', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('admin.academics.program.store'), [
            'code' => 'BSCS',
            'name' => 'Computer Science',
            'description' => null,
            'status' => 'Active',
        ])
        ->assertForbidden();

    expect(Program::query()->where('code', 'BSCS')->exists())->toBeFalse();
});

test('program permissions are seeded', function () {
    $this->seed(PermissionSeeder::class);

    expect(Permission::query()
        ->whereIn('name', [
            'admin view programs',
            'admin create programs',
            'admin update programs',
            'admin delete programs',
        ])
        ->count())->toBe(4);
});

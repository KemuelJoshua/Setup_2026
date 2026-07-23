<?php

use App\Models\Academics\GradeLevel;
use App\Models\User;
use Database\Seeders\permissions\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    foreach ([
        'admin view grade levels',
        'admin create grade levels',
        'admin update grade levels',
        'admin delete grade levels',
    ] as $permission) {
        Permission::create([
            'name' => $permission,
            'guard_name' => 'web',
        ]);
    }
});

test('authorized users can view and search grade levels', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin view grade levels');

    GradeLevel::query()->create(['name' => 'Grade 7']);

    $this
        ->actingAs($user)
        ->get(route('admin.academics.grade-level.index', ['search' => 'Grade 7']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/academics/grade-levels/Index')
            ->where('gradeLevels.total', 1)
            ->where('gradeLevels.data.0.name', 'Grade 7'));
});

test('authorized users can create a grade level', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create grade levels');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.grade-level.store'), [
            'name' => 'Grade 7',
        ])
        ->assertRedirect(route('admin.academics.grade-level.index'))
        ->assertSessionHas('success', 'Grade level created successfully.');

    $this->assertDatabaseHas('grade_levels', ['name' => 'Grade 7']);

    $gradeLevel = GradeLevel::query()->where('name', 'Grade 7')->firstOrFail();

    expect($gradeLevel->created_at)->not->toBeNull()
        ->and($gradeLevel->updated_at)->not->toBeNull();
});

test('authorized users can update a grade level', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update grade levels');
    $gradeLevel = GradeLevel::query()->create(['name' => 'Grade 7']);

    $this
        ->actingAs($user)
        ->put(route('admin.academics.grade-level.update', $gradeLevel), [
            'name' => 'Grade 8',
        ])
        ->assertRedirect(route('admin.academics.grade-level.index'))
        ->assertSessionHas('success', 'Grade level updated successfully.');

    expect($gradeLevel->refresh()->name)->toBe('Grade 8');
});

test('authorized users can delete a grade level', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin delete grade levels');
    $gradeLevel = GradeLevel::query()->create(['name' => 'Grade 7']);

    $this
        ->actingAs($user)
        ->delete(route('admin.academics.grade-level.destroy', $gradeLevel))
        ->assertRedirect(route('admin.academics.grade-level.index'))
        ->assertSessionHas('success', 'Grade level deleted successfully.');

    $this->assertModelMissing($gradeLevel);
});

test('grade level name is required', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create grade levels');

    $this
        ->actingAs($user)
        ->from(route('admin.academics.grade-level.index'))
        ->post(route('admin.academics.grade-level.store'), ['name' => ''])
        ->assertRedirect(route('admin.academics.grade-level.index'))
        ->assertSessionHasErrors(['name']);
});

test('users without permission cannot create a grade level', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('admin.academics.grade-level.store'), [
            'name' => 'Grade 7',
        ])
        ->assertForbidden();

    expect(GradeLevel::query()->where('name', 'Grade 7')->exists())->toBeFalse();
});

test('grade level permissions are seeded', function () {
    $this->seed(PermissionSeeder::class);

    expect(Permission::query()
        ->whereIn('name', [
            'admin view grade levels',
            'admin create grade levels',
            'admin update grade levels',
            'admin delete grade levels',
        ])
        ->count())->toBe(4);
});

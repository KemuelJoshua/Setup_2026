<?php

use App\Models\Academics\EducationalLevel;
use App\Models\Academics\GradeLevel;
use App\Models\User;
use Database\Seeders\permissions\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    foreach ([
        'admin view educational levels',
        'admin create educational levels',
        'admin update educational levels',
        'admin delete educational levels',
    ] as $permission) {
        Permission::create([
            'name' => $permission,
            'guard_name' => 'web',
        ]);
    }
});

test('authorized users can view and search educational levels', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin view educational levels');
    EducationalLevel::factory()->create(['name' => 'Junior High School']);
    EducationalLevel::factory()->create(['name' => 'Higher Education']);

    $this
        ->actingAs($user)
        ->get(route('admin.academics.educational-level.index', [
            'search' => 'Junior',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/academics/educational-levels/Index')
            ->where('educationalLevels.total', 1)
            ->where('educationalLevels.data.0.name', 'Junior High School'));
});

test('authorized users can create an educational level', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create educational levels');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.educational-level.store'), [
            'name' => 'Senior High School',
        ])
        ->assertRedirect(route('admin.academics.educational-level.index'))
        ->assertSessionHas('success', 'Educational level created successfully.');

    $educationalLevel = EducationalLevel::query()
        ->where('name', 'Senior High School')
        ->firstOrFail();

    expect($educationalLevel->created_at)->not->toBeNull()
        ->and($educationalLevel->updated_at)->not->toBeNull();
});

test('authorized users can update an educational level', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update educational levels');
    $educationalLevel = EducationalLevel::factory()->create([
        'name' => 'Secondary School',
    ]);

    $this
        ->actingAs($user)
        ->put(
            route('admin.academics.educational-level.update', $educationalLevel),
            ['name' => 'Junior High School'],
        )
        ->assertRedirect(route('admin.academics.educational-level.index'))
        ->assertSessionHas('success', 'Educational level updated successfully.');

    expect($educationalLevel->refresh()->name)->toBe('Junior High School');
});

test('authorized users can delete an unused educational level', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin delete educational levels');
    $educationalLevel = EducationalLevel::factory()->create();

    $this
        ->actingAs($user)
        ->delete(
            route('admin.academics.educational-level.destroy', $educationalLevel),
        )
        ->assertRedirect(route('admin.academics.educational-level.index'))
        ->assertSessionHas('success', 'Educational level deleted successfully.');

    $this->assertModelMissing($educationalLevel);
});

test('educational levels in use cannot be deleted', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin delete educational levels');
    $educationalLevel = EducationalLevel::factory()->create();
    GradeLevel::query()->create([
        'educational_level_id' => $educationalLevel->getKey(),
        'name' => 'Grade 7',
    ]);

    $this
        ->actingAs($user)
        ->delete(
            route('admin.academics.educational-level.destroy', $educationalLevel),
        )
        ->assertSessionHasErrors([
            'educational_level' => 'This educational level is in use and cannot be deleted.',
        ]);

    $this->assertModelExists($educationalLevel);
});

test('educational level names are required and unique', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create educational levels');
    EducationalLevel::factory()->create(['name' => 'Junior High School']);

    $this
        ->actingAs($user)
        ->from(route('admin.academics.educational-level.index'))
        ->post(route('admin.academics.educational-level.store'), [
            'name' => 'Junior High School',
        ])
        ->assertRedirect(route('admin.academics.educational-level.index'))
        ->assertSessionHasErrors(['name']);
});

test('users without permission cannot create an educational level', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('admin.academics.educational-level.store'), [
            'name' => 'Junior High School',
        ])
        ->assertForbidden();

    expect(EducationalLevel::query()->where('name', 'Junior High School')->exists())
        ->toBeFalse();
});

test('educational level permissions are seeded', function () {
    $this->seed(PermissionSeeder::class);

    expect(Permission::query()
        ->whereIn('name', [
            'admin view educational levels',
            'admin create educational levels',
            'admin update educational levels',
            'admin delete educational levels',
        ])
        ->count())->toBe(4);
});

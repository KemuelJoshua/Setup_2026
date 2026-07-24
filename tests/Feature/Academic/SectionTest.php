<?php

use App\Models\Academics\EducationalLevel;
use App\Models\Academics\Section;
use App\Models\User;
use Database\Seeders\permissions\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    foreach ([
        'admin view sections',
        'admin create sections',
        'admin update sections',
        'admin delete sections',
    ] as $permission) {
        Permission::create([
            'name' => $permission,
            'guard_name' => 'web',
        ]);
    }

    $this->educationalLevel = EducationalLevel::factory()->create([
        'name' => 'Junior High School',
    ]);
});

test('authorized users can view and search sections', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin view sections');

    Section::query()->create([
        'educational_level_id' => $this->educationalLevel->getKey(),
        'name' => 'Section A',
    ]);

    $this
        ->actingAs($user)
        ->get(route('admin.academics.section.index', ['search' => 'Section A']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/academics/sections/Index')
            ->where('sections.total', 1)
            ->where('sections.data.0.name', 'Section A')
            ->where('sections.data.0.educational_level.name', 'Junior High School')
            ->where('educationalLevels.0.name', 'Junior High School'));
});

test('authorized users can create a section', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create sections');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.section.store'), [
            'name' => 'Section A',
            'educational_level_id' => $this->educationalLevel->getKey(),
        ])
        ->assertRedirect(route('admin.academics.section.index'))
        ->assertSessionHas('success', 'Section created successfully.');

    $section = Section::query()->where('name', 'Section A')->firstOrFail();

    expect($section->educational_level_id)
        ->toBe($this->educationalLevel->getKey())
        ->and($section->educationalLevel->is($this->educationalLevel))->toBeTrue()
        ->and($section->created_at)->not->toBeNull()
        ->and($section->updated_at)->not->toBeNull();
});

test('authorized users can update a section', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update sections');
    $section = Section::query()->create([
        'educational_level_id' => $this->educationalLevel->getKey(),
        'name' => 'Section A',
    ]);

    $this
        ->actingAs($user)
        ->put(route('admin.academics.section.update', $section), [
            'name' => 'Section B',
            'educational_level_id' => $this->educationalLevel->getKey(),
        ])
        ->assertRedirect(route('admin.academics.section.index'))
        ->assertSessionHas('success', 'Section updated successfully.');

    expect($section->refresh()->name)->toBe('Section B');
});

test('authorized users can delete a section', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin delete sections');
    $section = Section::query()->create([
        'educational_level_id' => $this->educationalLevel->getKey(),
        'name' => 'Section A',
    ]);

    $this
        ->actingAs($user)
        ->delete(route('admin.academics.section.destroy', $section))
        ->assertRedirect(route('admin.academics.section.index'))
        ->assertSessionHas('success', 'Section deleted successfully.');

    $this->assertModelMissing($section);
});

test('section name is required', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create sections');

    $this
        ->actingAs($user)
        ->from(route('admin.academics.section.index'))
        ->post(route('admin.academics.section.store'), [
            'educational_level_id' => $this->educationalLevel->getKey(),
            'name' => '',
        ])
        ->assertRedirect(route('admin.academics.section.index'))
        ->assertSessionHasErrors(['name']);
});

test('section educational level is required and must exist', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create sections');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.section.store'), [
            'educational_level_id' => 999999,
            'name' => 'Section A',
        ])
        ->assertSessionHasErrors(['educational_level_id']);
});

test('users without permission cannot create a section', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('admin.academics.section.store'), [
            'name' => 'Section A',
            'educational_level_id' => $this->educationalLevel->getKey(),
        ])
        ->assertForbidden();

    expect(Section::query()->where('name', 'Section A')->exists())->toBeFalse();
});

test('section permissions are seeded', function () {
    $this->seed(PermissionSeeder::class);

    expect(Permission::query()
        ->whereIn('name', [
            'admin view sections',
            'admin create sections',
            'admin update sections',
            'admin delete sections',
        ])
        ->count())->toBe(4);
});

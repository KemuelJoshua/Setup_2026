<?php

use App\Models\Academics\EducationalLevel;
use App\Models\Academics\Subject;
use App\Models\User;
use Database\Seeders\permissions\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    foreach ([
        'admin view subjects',
        'admin create subjects',
        'admin update subjects',
        'admin delete subjects',
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

test('authorized users can view and search subjects', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin view subjects');

    Subject::query()->create([
        'educational_level_id' => $this->educationalLevel->getKey(),
        'name' => 'Mathematics',
    ]);

    $this
        ->actingAs($user)
        ->get(route('admin.academics.subject.index', ['search' => 'Mathematics']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/academics/subjects/Index')
            ->where('subjects.total', 1)
            ->where('subjects.data.0.name', 'Mathematics')
            ->where('subjects.data.0.educational_level.name', 'Junior High School')
            ->where('educationalLevels.0.name', 'Junior High School'));
});

test('authorized users can create a subject', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create subjects');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.subject.store'), [
            'name' => 'Mathematics',
            'educational_level_id' => $this->educationalLevel->getKey(),
        ])
        ->assertRedirect(route('admin.academics.subject.index'))
        ->assertSessionHas('success', 'Subject created successfully.');

    $subject = Subject::query()->where('name', 'Mathematics')->firstOrFail();

    expect($subject->educational_level_id)
        ->toBe($this->educationalLevel->getKey())
        ->and($subject->educationalLevel->is($this->educationalLevel))->toBeTrue()
        ->and($subject->created_at)->not->toBeNull()
        ->and($subject->updated_at)->not->toBeNull();
});

test('authorized users can update a subject', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update subjects');
    $subject = Subject::query()->create([
        'educational_level_id' => $this->educationalLevel->getKey(),
        'name' => 'Mathematics',
    ]);

    $this
        ->actingAs($user)
        ->put(route('admin.academics.subject.update', $subject), [
            'name' => 'Science',
            'educational_level_id' => $this->educationalLevel->getKey(),
        ])
        ->assertRedirect(route('admin.academics.subject.index'))
        ->assertSessionHas('success', 'Subject updated successfully.');

    expect($subject->refresh()->name)->toBe('Science');
});

test('authorized users can delete a subject', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin delete subjects');
    $subject = Subject::query()->create([
        'educational_level_id' => $this->educationalLevel->getKey(),
        'name' => 'Mathematics',
    ]);

    $this
        ->actingAs($user)
        ->delete(route('admin.academics.subject.destroy', $subject))
        ->assertRedirect(route('admin.academics.subject.index'))
        ->assertSessionHas('success', 'Subject deleted successfully.');

    $this->assertModelMissing($subject);
});

test('subject name is required', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create subjects');

    $this
        ->actingAs($user)
        ->from(route('admin.academics.subject.index'))
        ->post(route('admin.academics.subject.store'), [
            'educational_level_id' => $this->educationalLevel->getKey(),
            'name' => '',
        ])
        ->assertRedirect(route('admin.academics.subject.index'))
        ->assertSessionHasErrors(['name']);
});

test('subject educational level is required and must exist', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create subjects');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.subject.store'), [
            'educational_level_id' => 999999,
            'name' => 'Mathematics',
        ])
        ->assertSessionHasErrors(['educational_level_id']);
});

test('users without permission cannot create a subject', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('admin.academics.subject.store'), [
            'name' => 'Mathematics',
            'educational_level_id' => $this->educationalLevel->getKey(),
        ])
        ->assertForbidden();

    expect(Subject::query()->where('name', 'Mathematics')->exists())->toBeFalse();
});

test('subject permissions are seeded', function () {
    $this->seed(PermissionSeeder::class);

    expect(Permission::query()
        ->whereIn('name', [
            'admin view subjects',
            'admin create subjects',
            'admin update subjects',
            'admin delete subjects',
        ])
        ->count())->toBe(4);
});

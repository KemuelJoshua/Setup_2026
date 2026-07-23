<?php

use App\Models\Academics\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    foreach ([
        'admin view semesters',
        'admin create semesters',
        'admin update semesters',
        'admin delete semesters',
    ] as $permission) {
        Permission::create([
            'name' => $permission,
            'guard_name' => 'web',
        ]);
    }
});

test('authorized users can view and search semesters', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin view semesters');

    Semester::query()->create([
        'name' => 'First Semester',
        'code' => 'SEM-1',
        'start_date' => '2026-08-01',
        'end_date' => '2026-12-20',
    ]);

    $this
        ->actingAs($user)
        ->get(route('admin.academics.semester.index', ['search' => 'SEM-1']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/academics/semesters/Index')
            ->where('semesters.total', 1)
            ->where('semesters.data.0.name', 'First Semester')
            ->where('semesters.data.0.code', 'SEM-1'));
});

test('authorized users can create a semester', function () {

    $user = User::factory()->create();
    $user->givePermissionTo('admin create semesters');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.semester.store'), [
            'name' => 'First Semester',
            'code' => '2026-1',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-20',
        ])
        ->assertRedirect(route('admin.academics.semester.index'))
        ->assertSessionHas('success', 'Semester created successfully.');

    $this->assertDatabaseHas('semesters', [
        'name' => 'First Semester',
        'code' => '2026-1',
    ]);
});

test('authorized users can update a semester', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update semesters');
    $semester = Semester::query()->create([
        'name' => 'First Semester',
        'code' => 'SEM-1',
        'start_date' => '2026-08-01',
        'end_date' => '2026-12-20',
    ]);

    $this
        ->actingAs($user)
        ->put(route('admin.academics.semester.update', $semester), [
            'name' => 'Updated Semester',
            'code' => 'SEM-UPDATED',
            'start_date' => '2026-08-15',
            'end_date' => '2026-12-21',
        ])
        ->assertRedirect(route('admin.academics.semester.index'))
        ->assertSessionHas('success', 'Semester updated successfully.');

    $semester->refresh();

    expect($semester->name)->toBe('Updated Semester')
        ->and($semester->code)->toBe('SEM-UPDATED');
});

test('authorized users can delete a semester', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin delete semesters');
    $semester = Semester::query()->create([
        'name' => 'First Semester',
        'code' => 'SEM-1',
        'start_date' => '2026-08-01',
        'end_date' => '2026-12-20',
    ]);

    $this
        ->actingAs($user)
        ->delete(route('admin.academics.semester.destroy', $semester))
        ->assertRedirect(route('admin.academics.semester.index'))
        ->assertSessionHas('success', 'Semester deleted successfully.');

    $this->assertModelMissing($semester);
});

test('semester forms validate required fields and date order', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create semesters');

    $this
        ->actingAs($user)
        ->from(route('admin.academics.semester.index'))
        ->post(route('admin.academics.semester.store'), [
            'name' => '',
            'code' => '',
            'start_date' => '2026-08-01',
            'end_date' => '2026-07-31',
        ])
        ->assertRedirect(route('admin.academics.semester.index'))
        ->assertSessionHasErrors(['name', 'code', 'end_date']);
});

test('users without permission cannot create a semester', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('admin.academics.semester.store'), [
            'name' => 'First Semester',
            'code' => 'SEM-1',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-20',
        ])
        ->assertForbidden();

    expect(Semester::query()->where('code', 'SEM-1')->exists())->toBeFalse();
});

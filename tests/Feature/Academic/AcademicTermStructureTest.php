<?php

use App\Enums\AcademicStatus;
use App\Enums\AcademicTermStructureType;
use App\Models\Academics\AcademicPeriod;
use App\Models\Academics\AcademicTermStructure;
use App\Models\User;
use Database\Seeders\permissions\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    foreach ([
        'admin view academic term structures',
        'admin create academic term structures',
        'admin update academic term structures',
        'admin delete academic term structures',
    ] as $permission) {
        Permission::findOrCreate($permission, 'web');
    }
});

test('authorized users can list structures with ordered root periods and children', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin view academic term structures');
    $structure = AcademicTermStructure::factory()->create([
        'name' => 'College',
        'code' => 'COLLEGE',
    ]);
    $secondSemester = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $structure->getKey(),
        'name' => 'Second Semester',
        'sequence' => 2,
    ]);
    $firstSemester = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $structure->getKey(),
        'name' => 'First Semester',
        'sequence' => 1,
    ]);
    AcademicPeriod::factory()->childOf($firstSemester)->create([
        'name' => 'Final',
        'sequence' => 2,
    ]);
    AcademicPeriod::factory()->childOf($firstSemester)->create([
        'name' => 'Prelim',
        'sequence' => 1,
    ]);

    $this
        ->actingAs($user)
        ->get(route('admin.academics.academic-term-structures.index'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/academics/academic-term-structures/Index')
            ->where('academicTermStructures.total', 1)
            ->where('academicTermStructures.data.0.name', 'College')
            ->where('academicTermStructures.data.0.type', 'semester')
            ->where('academicTermStructures.data.0.root_periods.0.name', 'First Semester')
            ->where('academicTermStructures.data.0.root_periods.0.children.0.name', 'Prelim')
            ->where('academicTermStructures.data.0.root_periods.0.children.1.name', 'Final')
            ->where('academicTermStructures.data.0.root_periods.1.name', $secondSemester->name));
});

test('authorized users can create a structure', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create academic term structures');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.academic-term-structures.store'), [
            'name' => 'Senior High School',
            'code' => 'SHS',
            'type' => AcademicTermStructureType::Quarterly->value,
            'status' => AcademicStatus::Active->value,
        ])
        ->assertRedirect(route('admin.academics.academic-term-structures.index'));

    $structure = AcademicTermStructure::query()->where('code', 'SHS')->firstOrFail();

    expect($structure->name)->toBe('Senior High School')
        ->and($structure->type)->toBe(AcademicTermStructureType::Quarterly)
        ->and($structure->status)->toBe(AcademicStatus::Active);
});

test('authorized users can update a structure including its status', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update academic term structures');
    $structure = AcademicTermStructure::factory()->create();

    $this
        ->actingAs($user)
        ->put(route('admin.academics.academic-term-structures.update', $structure), [
            'name' => 'Updated Structure',
            'code' => 'UPDATED',
            'type' => AcademicTermStructureType::Trisem->value,
            'status' => AcademicStatus::Inactive->value,
        ])
        ->assertRedirect(route('admin.academics.academic-term-structures.index'));

    expect($structure->refresh()->name)->toBe('Updated Structure')
        ->and($structure->code)->toBe('UPDATED')
        ->and($structure->type)->toBe(AcademicTermStructureType::Trisem)
        ->and($structure->status)->toBe(AcademicStatus::Inactive);
});

test('authorized users can delete a structure and all of its periods', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin delete academic term structures');
    $structure = AcademicTermStructure::factory()->create();
    $root = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $structure->getKey(),
        'sequence' => 1,
    ]);
    $child = AcademicPeriod::factory()->childOf($root)->create(['sequence' => 1]);

    $this
        ->actingAs($user)
        ->delete(route('admin.academics.academic-term-structures.destroy', $structure))
        ->assertRedirect(route('admin.academics.academic-term-structures.index'));

    $this->assertModelMissing($structure);
    $this->assertModelMissing($root);
    $this->assertModelMissing($child);
});

test('structure code must be unique', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create academic term structures');
    AcademicTermStructure::factory()->create(['code' => 'COLLEGE']);

    $this
        ->actingAs($user)
        ->post(route('admin.academics.academic-term-structures.store'), [
            'name' => 'Another College',
            'code' => 'COLLEGE',
            'type' => AcademicTermStructureType::Semester->value,
            'status' => AcademicStatus::Active->value,
        ])
        ->assertSessionHasErrors('code');
});

test('invalid structure status is rejected', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create academic term structures');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.academic-term-structures.store'), [
            'name' => 'College',
            'code' => 'COLLEGE',
            'type' => AcademicTermStructureType::Semester->value,
            'status' => 'archived',
        ])
        ->assertSessionHasErrors('status');
});

test('invalid structure type is rejected', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create academic term structures');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.academic-term-structures.store'), [
            'name' => 'College',
            'code' => 'COLLEGE',
            'type' => 'monthly',
            'status' => AcademicStatus::Active->value,
        ])
        ->assertSessionHasErrors('type');
});

test('users without permission cannot manage structures', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('admin.academics.academic-term-structures.store'), [
            'name' => 'College',
            'code' => 'COLLEGE',
            'type' => AcademicTermStructureType::Semester->value,
            'status' => AcademicStatus::Active->value,
        ])
        ->assertForbidden();
});

test('a structure with grading periods cannot be changed to quarterly', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update academic term structures');
    $structure = AcademicTermStructure::factory()->create();
    $root = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $structure->getKey(),
        'sequence' => 1,
    ]);
    AcademicPeriod::factory()->childOf($root)->create(['sequence' => 1]);

    $this
        ->actingAs($user)
        ->put(route('admin.academics.academic-term-structures.update', $structure), [
            'name' => $structure->name,
            'code' => $structure->code,
            'type' => AcademicTermStructureType::Quarterly->value,
            'status' => $structure->status->value,
        ])
        ->assertSessionHasErrors('type');

    expect($structure->refresh()->type)->toBe(AcademicTermStructureType::Semester);
});

test('academic structure and period permissions are seeded', function () {
    $this->seed(PermissionSeeder::class);

    expect(Permission::query()
        ->whereIn('name', [
            'admin view academic term structures',
            'admin create academic term structures',
            'admin update academic term structures',
            'admin delete academic term structures',
            'admin create academic periods',
            'admin update academic periods',
            'admin delete academic periods',
        ])
        ->count())->toBe(7);
});

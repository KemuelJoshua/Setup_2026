<?php

use App\Models\Academics\AcademicPeriod;
use App\Models\Academics\AcademicTermStructure;
use App\Models\Academics\Curriculum;
use App\Models\Academics\CurriculumSubject;
use App\Models\Academics\GradeLevel;
use App\Models\Academics\Subject;
use App\Models\User;
use Database\Seeders\permissions\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    foreach ([
        'admin view curricula',
        'admin create curricula',
        'admin update curricula',
        'admin delete curricula',
    ] as $permission) {
        Permission::create([
            'name' => $permission,
            'guard_name' => 'web',
        ]);
    }
});

function curriculumSubjectOptions(): array
{
    $structure = AcademicTermStructure::factory()->quarterly()->create();

    return [
        Subject::query()->create(['name' => 'Mathematics']),
        GradeLevel::query()->create(['name' => 'Grade 7']),
        AcademicPeriod::factory()->create([
            'academic_term_structure_id' => $structure->getKey(),
            'name' => '1st Quarter',
            'code' => 'Q1',
            'sequence' => 1,
        ]),
    ];
}

test('authorized users can view and search curricula', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin view curricula');
    [$subject, $yearLevel, $academicPeriod] = curriculumSubjectOptions();

    $curriculum = Curriculum::query()->create([
        'code' => 'JHS-2026',
        'name' => 'Junior High School Curriculum',
        'effective_year' => 2026,
        'description' => 'Current curriculum',
        'status' => 'Active',
    ]);
    $curriculum->curriculumSubjects()->create([
        'subject_id' => $subject->getKey(),
        'year_level_id' => $yearLevel->getKey(),
        'academic_period_id' => $academicPeriod->getKey(),
        'is_required' => true,
        'sort_order' => 1,
    ]);

    $this
        ->actingAs($user)
        ->get(route('admin.academics.curriculum.index', ['search' => 'JHS']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/academics/curricula/Index')
            ->where('curricula.total', 1)
            ->where('curricula.data.0.code', 'JHS-2026')
            ->where('curricula.data.0.curriculum_subjects_count', 1)
            ->missing('subjects')
            ->missing('yearLevels')
            ->missing('academicPeriods'));
});

test('authorized users can open the create curriculum page', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create curricula');
    curriculumSubjectOptions();

    $this
        ->actingAs($user)
        ->get(route('admin.academics.curriculum.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/academics/curricula/Form')
            ->where('curriculum', null)
            ->where('subjects.0.name', 'Mathematics')
            ->where('yearLevels.0.name', 'Grade 7')
            ->where('academicPeriods.0.code', 'Q1')
            ->where('academicPeriods.0.structure.type', 'quarterly'));
});

test('authorized users can open the edit curriculum page', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update curricula');
    [$subject, $yearLevel, $academicPeriod] = curriculumSubjectOptions();

    $curriculum = Curriculum::query()->create([
        'code' => 'JHS-2026',
        'name' => 'Junior High School Curriculum',
        'effective_year' => 2026,
        'description' => null,
        'status' => 'Active',
    ]);
    $curriculum->curriculumSubjects()->create([
        'subject_id' => $subject->getKey(),
        'year_level_id' => $yearLevel->getKey(),
        'academic_period_id' => $academicPeriod->getKey(),
        'is_required' => true,
        'sort_order' => 1,
    ]);

    $this
        ->actingAs($user)
        ->get(route('admin.academics.curriculum.edit', $curriculum))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/academics/curricula/Form')
            ->where('curriculum.id', $curriculum->getKey())
            ->where('curriculum.code', 'JHS-2026')
            ->where('curriculum.curriculum_subjects.0.subject_id', $subject->getKey())
            ->where('curriculum.curriculum_subjects.0.year_level_id', $yearLevel->getKey())
            ->where('curriculum.curriculum_subjects.0.academic_period_id', $academicPeriod->getKey()));
});

test('authorized users can create a curriculum with subjects', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create curricula');
    [$subject, $yearLevel, $academicPeriod] = curriculumSubjectOptions();

    $this
        ->actingAs($user)
        ->post(route('admin.academics.curriculum.store'), [
            'code' => 'JHS-2026',
            'name' => 'Junior High School Curriculum',
            'effective_year' => 2026,
            'description' => null,
            'status' => 'Active',
            'curriculum_subjects' => [[
                'subject_id' => $subject->getKey(),
                'year_level_id' => $yearLevel->getKey(),
                'academic_period_id' => $academicPeriod->getKey(),
                'is_required' => true,
                'sort_order' => 1,
            ]],
        ])
        ->assertRedirect(route('admin.academics.curriculum.index'))
        ->assertSessionHas('success', 'Curriculum created successfully.');

    $curriculum = Curriculum::query()->where('code', 'JHS-2026')->firstOrFail();
    $curriculumSubject = $curriculum->curriculumSubjects()->firstOrFail();

    expect($curriculum->effective_year)->toBe(2026)
        ->and($curriculum->description)->toBeNull()
        ->and($curriculum->created_at)->not->toBeNull()
        ->and($curriculumSubject->subject_id)->toBe($subject->getKey())
        ->and($curriculumSubject->is_required)->toBeTrue()
        ->and($curriculumSubject->sort_order)->toBe(1)
        ->and($curriculumSubject->created_at)->not->toBeNull();
});

test('authorized users can update a curriculum and replace its subjects', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update curricula');
    [$originalSubject, $yearLevel, $academicPeriod] = curriculumSubjectOptions();
    $replacementSubject = Subject::query()->create(['name' => 'Science']);

    $curriculum = Curriculum::query()->create([
        'code' => 'JHS-2026',
        'name' => 'Junior High School Curriculum',
        'effective_year' => 2026,
        'description' => null,
        'status' => 'Active',
    ]);
    $originalAssignment = $curriculum->curriculumSubjects()->create([
        'subject_id' => $originalSubject->getKey(),
        'year_level_id' => $yearLevel->getKey(),
        'academic_period_id' => $academicPeriod->getKey(),
        'is_required' => true,
        'sort_order' => 1,
    ]);

    $this
        ->actingAs($user)
        ->put(route('admin.academics.curriculum.update', $curriculum), [
            'code' => 'JHS-2027',
            'name' => 'Updated Junior High School Curriculum',
            'effective_year' => 2027,
            'description' => 'Revised curriculum',
            'status' => 'Draft',
            'curriculum_subjects' => [[
                'subject_id' => $replacementSubject->getKey(),
                'year_level_id' => $yearLevel->getKey(),
                'academic_period_id' => $academicPeriod->getKey(),
                'is_required' => false,
                'sort_order' => 2,
            ]],
        ])
        ->assertRedirect(route('admin.academics.curriculum.index'))
        ->assertSessionHas('success', 'Curriculum updated successfully.');

    $replacementAssignment = $curriculum->refresh()
        ->curriculumSubjects()
        ->firstOrFail();

    expect($curriculum->code)->toBe('JHS-2027')
        ->and($curriculum->effective_year)->toBe(2027)
        ->and($curriculum->status)->toBe('Draft')
        ->and($replacementAssignment->subject_id)->toBe($replacementSubject->getKey())
        ->and($replacementAssignment->is_required)->toBeFalse()
        ->and(CurriculumSubject::query()->whereKey($originalAssignment)->exists())
        ->toBeFalse();
});

test('deleting a curriculum also deletes its subject assignments', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin delete curricula');
    [$subject, $yearLevel, $academicPeriod] = curriculumSubjectOptions();

    $curriculum = Curriculum::query()->create([
        'code' => 'JHS-2026',
        'name' => 'Junior High School Curriculum',
        'effective_year' => 2026,
        'description' => null,
        'status' => 'Active',
    ]);
    $assignment = $curriculum->curriculumSubjects()->create([
        'subject_id' => $subject->getKey(),
        'year_level_id' => $yearLevel->getKey(),
        'academic_period_id' => $academicPeriod->getKey(),
        'is_required' => true,
        'sort_order' => 1,
    ]);

    $this
        ->actingAs($user)
        ->delete(route('admin.academics.curriculum.destroy', $curriculum))
        ->assertRedirect(route('admin.academics.curriculum.index'));

    $this->assertModelMissing($curriculum);
    $this->assertModelMissing($assignment);
});

test('curriculum forms validate curriculum and subject fields', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create curricula');

    Curriculum::query()->create([
        'code' => 'JHS-2026',
        'name' => 'Existing Curriculum',
        'effective_year' => 2026,
        'description' => null,
        'status' => 'Active',
    ]);

    $this
        ->actingAs($user)
        ->from(route('admin.academics.curriculum.create'))
        ->post(route('admin.academics.curriculum.store'), [
            'code' => 'JHS-2026',
            'name' => '',
            'effective_year' => 1000,
            'status' => '',
            'curriculum_subjects' => [[
                'subject_id' => 999,
                'year_level_id' => 999,
                'academic_period_id' => 999,
                'is_required' => 'invalid',
                'sort_order' => -1,
            ]],
        ])
        ->assertRedirect(route('admin.academics.curriculum.create'))
        ->assertSessionHasErrors([
            'code',
            'name',
            'effective_year',
            'status',
            'curriculum_subjects.0.subject_id',
            'curriculum_subjects.0.year_level_id',
            'curriculum_subjects.0.academic_period_id',
            'curriculum_subjects.0.is_required',
            'curriculum_subjects.0.sort_order',
        ]);
});

test('users without permission cannot create a curriculum', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('admin.academics.curriculum.store'), [
            'code' => 'JHS-2026',
            'name' => 'Junior High School Curriculum',
            'effective_year' => 2026,
            'description' => null,
            'status' => 'Active',
        ])
        ->assertForbidden();

    expect(Curriculum::query()->where('code', 'JHS-2026')->exists())->toBeFalse();
});

test('users without permission cannot open curriculum form pages', function () {
    $user = User::factory()->create();
    $curriculum = Curriculum::query()->create([
        'code' => 'JHS-2026',
        'name' => 'Junior High School Curriculum',
        'effective_year' => 2026,
        'description' => null,
        'status' => 'Active',
    ]);

    $this->actingAs($user)
        ->get(route('admin.academics.curriculum.create'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('admin.academics.curriculum.edit', $curriculum))
        ->assertForbidden();
});

test('curriculum permissions are seeded', function () {
    $this->seed(PermissionSeeder::class);

    expect(Permission::query()
        ->whereIn('name', [
            'admin view curricula',
            'admin create curricula',
            'admin update curricula',
            'admin delete curricula',
        ])
        ->count())->toBe(4);
});

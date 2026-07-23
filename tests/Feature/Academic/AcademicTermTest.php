<?php

use App\Models\Academics\AcademicTerm;
use App\Models\Academics\GradingPeriod;
use App\Models\User;
use Database\Seeders\permissions\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    foreach ([
        'admin view academic terms',
        'admin create academic terms',
        'admin update academic terms',
        'admin delete academic terms',
    ] as $permission) {
        Permission::create([
            'name' => $permission,
            'guard_name' => 'web',
        ]);
    }
});

test('academic terms use the renamed table and curriculum key', function () {
    expect(Schema::hasTable('academic_terms'))->toBeTrue()
        ->and(Schema::hasTable('semesters'))->toBeFalse()
        ->and(Schema::hasTable('grading_periods'))->toBeTrue()
        ->and(Schema::hasColumn('academic_terms', 'type'))->toBeTrue()
        ->and(Schema::hasColumn('academic_terms', 'parent_id'))->toBeFalse()
        ->and(Schema::hasColumns('grading_periods', [
            'academic_term_id',
            'name',
            'code',
            'sort_order',
        ]))->toBeTrue()
        ->and(Schema::hasColumn('curriculum_subjects', 'academic_term_id'))->toBeTrue()
        ->and(Schema::hasColumn('curriculum_subjects', 'semester_id'))->toBeFalse();
});

test('authorized users can view and search academic terms', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin view academic terms');

    $academicTerm = AcademicTerm::query()->create([
        'name' => '1st Quarter',
        'code' => 'Q1',
        'type' => 'Quarter',
    ]);
    $academicTerm->gradingPeriods()->create([
        'name' => 'Quarter Exam',
        'code' => 'EXAM',
        'sort_order' => 1,
    ]);

    $this
        ->actingAs($user)
        ->get(route('admin.academics.academic-term.index', ['search' => 'Q1']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/academics/academic-terms/Index')
            ->where('academicTerms.total', 1)
            ->where('academicTerms.per_page', 25)
            ->where('academicTerms.data.0.name', '1st Quarter')
            ->where('academicTerms.data.0.code', 'Q1')
            ->where('academicTerms.data.0.type', 'Quarter')
            ->where(
                'academicTerms.data.0.grading_periods.0.name',
                'Quarter Exam',
            )
            ->missing('parentOptions'));
});

test('authorized users can create an academic term with grading periods', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create academic terms');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.academic-term.store'), [
            'name' => '1st Semester',
            'code' => '1ST',
            'type' => 'Semester',
            'grading_periods' => [
                ['name' => 'Prelim', 'code' => 'PRE', 'sort_order' => 1],
                ['name' => 'Finals', 'code' => 'FIN', 'sort_order' => 2],
            ],
        ])
        ->assertRedirect(route('admin.academics.academic-term.index'))
        ->assertSessionHas('success', 'Academic term created successfully.');

    $academicTerm = AcademicTerm::query()->where('code', '1ST')->firstOrFail();

    expect($academicTerm->gradingPeriods()->pluck('code')->all())
        ->toBe(['PRE', 'FIN']);
});

test('authorized users can update an academic term and its grading periods', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update academic terms');
    $academicTerm = AcademicTerm::query()->create([
        'name' => '1st Quarter',
        'code' => 'Q1',
        'type' => 'Quarter',
    ]);
    $academicTerm->gradingPeriods()->create([
        'name' => 'Old Period',
        'code' => 'OLD',
        'sort_order' => 1,
    ]);

    $this
        ->actingAs($user)
        ->put(route('admin.academics.academic-term.update', $academicTerm), [
            'name' => 'First Quarter',
            'code' => 'Q1',
            'type' => 'Quarter',
            'grading_periods' => [
                ['name' => 'Quarter Exam', 'code' => 'EXAM', 'sort_order' => 1],
            ],
        ])
        ->assertRedirect(route('admin.academics.academic-term.index'))
        ->assertSessionHas('success', 'Academic term updated successfully.');

    expect($academicTerm->refresh()->name)->toBe('First Quarter')
        ->and($academicTerm->gradingPeriods()->pluck('code')->all())
        ->toBe(['EXAM']);
});

test('authorized users can delete an academic term', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin delete academic terms');
    $academicTerm = AcademicTerm::query()->create([
        'name' => 'Summer',
        'code' => 'SUM',
        'type' => 'Not Applicable',
    ]);
    $gradingPeriod = $academicTerm->gradingPeriods()->create([
        'name' => 'Finals',
        'code' => 'FIN',
        'sort_order' => 1,
    ]);

    $this
        ->actingAs($user)
        ->delete(route('admin.academics.academic-term.destroy', $academicTerm))
        ->assertRedirect(route('admin.academics.academic-term.index'))
        ->assertSessionHas('success', 'Academic term deleted successfully.');

    $this->assertModelMissing($academicTerm);
    $this->assertModelMissing($gradingPeriod);
});

test('academic term forms validate required fields and allowed types', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create academic terms');

    $this
        ->actingAs($user)
        ->from(route('admin.academics.academic-term.index'))
        ->post(route('admin.academics.academic-term.store'), [
            'name' => '',
            'code' => '',
            'type' => 'Invalid',
        ])
        ->assertRedirect(route('admin.academics.academic-term.index'))
        ->assertSessionHasErrors(['name', 'code', 'type']);
});

test('academic term grading periods validate required fields and duplicate codes', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create academic terms');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.academic-term.store'), [
            'name' => '1st Semester',
            'code' => '1ST',
            'type' => 'Semester',
            'grading_periods' => [
                ['name' => '', 'code' => 'PRE', 'sort_order' => 1],
                ['name' => 'Prelim', 'code' => 'pre', 'sort_order' => 2],
            ],
        ])
        ->assertSessionHasErrors([
            'grading_periods.0.name',
            'grading_periods.1.code',
        ]);

    expect(AcademicTerm::query()->where('code', '1ST')->exists())->toBeFalse()
        ->and(GradingPeriod::query()->exists())->toBeFalse();
});

test('users without permission cannot create an academic term', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('admin.academics.academic-term.store'), [
            'name' => '1st Quarter',
            'code' => 'Q1',
            'type' => 'Quarter',
        ])
        ->assertForbidden();

    expect(AcademicTerm::query()->where('code', 'Q1')->exists())->toBeFalse();
});

test('academic term permissions are seeded', function () {
    $this->seed(PermissionSeeder::class);

    expect(Permission::query()
        ->whereIn('name', [
            'admin view academic terms',
            'admin create academic terms',
            'admin update academic terms',
            'admin delete academic terms',
        ])
        ->count())->toBe(4);
});

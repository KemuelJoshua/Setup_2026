<?php

use App\Models\Academics\AcademicPeriod;
use App\Models\Academics\AcademicTermStructure;
use App\Models\Academics\Curriculum;
use App\Models\Academics\EducationalLevel;
use App\Models\Academics\GradeLevel;
use App\Models\Academics\Program;
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

/**
 * @return array{
 *     structure: AcademicTermStructure,
 *     period: AcademicPeriod,
 *     educationalLevel: EducationalLevel,
 *     yearLevel: GradeLevel,
 *     program: Program,
 *     subject: Subject
 * }
 */
function curriculumOptions(): array
{
    $educationalLevel = EducationalLevel::factory()->create([
        'name' => 'Junior High School',
    ]);
    $structure = AcademicTermStructure::factory()->quarterly()->create([
        'educational_level_id' => $educationalLevel->getKey(),
        'name' => 'JHS — Quarter',
        'code' => 'JHS4Q',
    ]);
    $period = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $structure->getKey(),
        'name' => 'First Quarter',
        'code' => 'Q1',
        'sequence' => 1,
    ]);

    return [
        'structure' => $structure,
        'period' => $period,
        'educationalLevel' => $educationalLevel,
        'yearLevel' => GradeLevel::query()->create([
            'educational_level_id' => $educationalLevel->getKey(),
            'name' => 'Grade 7',
        ]),
        'program' => Program::query()->create([
            'educational_level_id' => $educationalLevel->getKey(),
            'code' => 'JHS',
            'name' => 'Junior High School',
            'status' => 'Active',
        ]),
        'subject' => Subject::query()->create(['name' => 'Mathematics']),
    ];
}

function createCurriculum(array $options, array $overrides = []): Curriculum
{
    return Curriculum::query()->create([
        'code' => 'JHS-2026',
        'name' => 'Junior High School Curriculum',
        'program_id' => $options['program']->getKey(),
        'academic_term_structure_id' => $options['structure']->getKey(),
        'effective_year' => 2026,
        'number_of_years' => 4,
        'description' => null,
        'status' => 'Active',
        ...$overrides,
    ]);
}

test('authorized users can view and search curricula', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin view curricula');
    $options = curriculumOptions();
    $curriculum = createCurriculum($options);
    $curriculum->curriculumSubjects()->create([
        'subject_id' => $options['subject']->getKey(),
        'year_level_id' => $options['yearLevel']->getKey(),
        'academic_period_id' => $options['period']->getKey(),
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
            ->where('curricula.data.0.program', 'Junior High School')
            ->where('curricula.data.0.academic_structure', 'JHS — Quarter')
            ->where('curricula.data.0.curriculum_subjects_count', 1)
            ->missing('subjects'));
});

test('authorized users can open the create curriculum page', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create curricula');
    $options = curriculumOptions();
    $seniorHighSchool = EducationalLevel::factory()->create([
        'name' => 'Senior High School',
    ]);
    $seniorHighStructure = AcademicTermStructure::factory()->quarterly()->create([
        'educational_level_id' => $seniorHighSchool->getKey(),
        'name' => 'SHS — Quarter',
        'code' => 'SHS4Q',
    ]);
    Program::query()->create([
        'educational_level_id' => $seniorHighSchool->getKey(),
        'code' => 'SHS',
        'name' => 'Senior High School',
        'status' => 'Active',
    ]);

    $this
        ->actingAs($user)
        ->get(route('admin.academics.curriculum.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/academics/curricula/Form')
            ->where(
                'educationalLevels.0.id',
                $options['educationalLevel']->getKey(),
            )
            ->where('programs.0.code', 'JHS')
            ->where(
                'programs.0.educational_level_id',
                $options['educationalLevel']->getKey(),
            )
            ->where('programs.1.code', 'SHS')
            ->where(
                'programs.1.educational_level_id',
                $seniorHighSchool->getKey(),
            )
            ->where('academicStructures.0.code', 'JHS4Q')
            ->where(
                'academicStructures.0.educational_level_id',
                $options['educationalLevel']->getKey(),
            )
            ->where('academicStructures.0.root_periods.0.code', 'Q1')
            ->where('academicStructures.1.code', $seniorHighStructure->code)
            ->where(
                'academicStructures.1.educational_level_id',
                $seniorHighSchool->getKey(),
            )
            ->missing('schoolYears'));
});

test('authorized users can open the curriculum subject builder', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update curricula');
    $options = curriculumOptions();
    $curriculum = createCurriculum($options);
    $assignment = $curriculum->curriculumSubjects()->create([
        'subject_id' => $options['subject']->getKey(),
        'year_level_id' => $options['yearLevel']->getKey(),
        'academic_period_id' => $options['period']->getKey(),
        'units' => 3,
        'lecture_hours' => 2,
        'laboratory_hours' => 1,
        'is_required' => true,
        'sort_order' => 1,
    ]);

    $this
        ->actingAs($user)
        ->get(route('admin.academics.curriculum.edit', $curriculum))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/academics/curricula/Subjects')
            ->where('curriculum.id', $curriculum->getKey())
            ->where('curriculum.academic_structure.code', 'JHS4Q')
            ->where('curriculum.curriculum_subjects.0.id', $assignment->getKey())
            ->where('curriculum.curriculum_subjects.0.subject_name', 'Mathematics')
            ->where('curriculum.curriculum_subjects.0.units', '3.00'));
});

test('authorized users create basic curriculum information before adding subjects', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create curricula');
    $options = curriculumOptions();

    $response = $this
        ->actingAs($user)
        ->post(route('admin.academics.curriculum.store'), [
            'code' => 'JHS-2026',
            'name' => 'Junior High School Curriculum',
            'program_id' => $options['program']->getKey(),
            'academic_term_structure_id' => $options['structure']->getKey(),
            'effective_year' => 2026,
            'number_of_years' => 4,
            'description' => null,
            'status' => 'Active',
        ]);

    $curriculum = Curriculum::query()->where('code', 'JHS-2026')->firstOrFail();
    $response->assertRedirect(route('admin.academics.curriculum.edit', $curriculum));

    expect($curriculum->program_id)->toBe($options['program']->getKey())
        ->and($curriculum->academic_term_structure_id)->toBe($options['structure']->getKey())
        ->and($curriculum->effective_year)->toBe(2026)
        ->and($curriculum->number_of_years)->toBe(4)
        ->and($curriculum->curriculumSubjects)->toBeEmpty();

});

test('curriculum program and academic structure must use the same educational level', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create curricula');
    $options = curriculumOptions();
    $otherEducationalLevel = EducationalLevel::factory()->create();
    $otherStructure = AcademicTermStructure::factory()->create([
        'educational_level_id' => $otherEducationalLevel->getKey(),
    ]);

    $this
        ->actingAs($user)
        ->post(route('admin.academics.curriculum.store'), [
            'code' => 'INVALID-LEVEL',
            'name' => 'Invalid Level Curriculum',
            'program_id' => $options['program']->getKey(),
            'academic_term_structure_id' => $otherStructure->getKey(),
            'effective_year' => 2026,
            'number_of_years' => 4,
            'status' => 'Draft',
        ])
        ->assertSessionHasErrors('academic_term_structure_id');
});

test('authorized users can update curriculum details but not its academic structure', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update curricula');
    $options = curriculumOptions();
    $curriculum = createCurriculum($options);
    $otherStructure = AcademicTermStructure::factory()->create();

    $this
        ->actingAs($user)
        ->put(route('admin.academics.curriculum.update', $curriculum), [
            'code' => 'JHS-UPDATED',
            'name' => 'Updated Curriculum',
            'program_id' => $options['program']->getKey(),
            'academic_term_structure_id' => $otherStructure->getKey(),
            'effective_year' => 2027,
            'number_of_years' => 4,
            'description' => null,
            'status' => 'Draft',
        ])
        ->assertSessionHasErrors('academic_term_structure_id');

    expect($curriculum->refresh()->academic_term_structure_id)
        ->toBe($options['structure']->getKey());
});

test('subjects can reference prerequisites and corequisites in the same curriculum', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update curricula');
    $options = curriculumOptions();
    $curriculum = createCurriculum($options);
    $programmingOne = $curriculum->curriculumSubjects()->create([
        'subject_id' => $options['subject']->getKey(),
        'year_level_id' => $options['yearLevel']->getKey(),
        'academic_period_id' => $options['period']->getKey(),
        'is_required' => true,
        'sort_order' => 1,
    ]);
    $programmingTwo = Subject::query()->create(['name' => 'Programming 2']);
    $discreteMathematics = Subject::query()->create(['name' => 'Discrete Mathematics']);
    $corequisite = $curriculum->curriculumSubjects()->create([
        'subject_id' => $discreteMathematics->getKey(),
        'year_level_id' => $options['yearLevel']->getKey(),
        'academic_period_id' => $options['period']->getKey(),
        'is_required' => true,
        'sort_order' => 2,
    ]);

    $this
        ->actingAs($user)
        ->post(route('admin.academics.curriculum.subjects.store', $curriculum), [
            'subject_id' => $programmingTwo->getKey(),
            'year_level_id' => $options['yearLevel']->getKey(),
            'academic_period_id' => $options['period']->getKey(),
            'units' => 3,
            'lecture_hours' => 2,
            'laboratory_hours' => 1,
            'sort_order' => 3,
            'remarks' => 'Major course',
            'prerequisite_ids' => [$programmingOne->getKey()],
            'corequisite_ids' => [$corequisite->getKey()],
        ])
        ->assertRedirect();

    $assignment = $curriculum->curriculumSubjects()
        ->whereBelongsTo($programmingTwo, 'subject')
        ->firstOrFail();

    expect($assignment->units)->toBe('3.00')
        ->and($assignment->prerequisites()->pluck('curriculum_subjects.id')->all())
        ->toBe([$programmingOne->getKey()])
        ->and($assignment->corequisites()->pluck('curriculum_subjects.id')->all())
        ->toBe([$corequisite->getKey()]);
});

test('curriculum subject references cannot cross curricula or reference themselves', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update curricula');
    $options = curriculumOptions();
    $curriculum = createCurriculum($options);
    $assignment = $curriculum->curriculumSubjects()->create([
        'subject_id' => $options['subject']->getKey(),
        'year_level_id' => $options['yearLevel']->getKey(),
        'academic_period_id' => $options['period']->getKey(),
        'is_required' => true,
        'sort_order' => 1,
    ]);
    $otherCurriculum = createCurriculum($options, ['code' => 'OTHER']);
    $otherAssignment = $otherCurriculum->curriculumSubjects()->create([
        'subject_id' => $options['subject']->getKey(),
        'year_level_id' => $options['yearLevel']->getKey(),
        'academic_period_id' => $options['period']->getKey(),
        'is_required' => true,
        'sort_order' => 1,
    ]);

    $this
        ->actingAs($user)
        ->put(route('admin.academics.curriculum.subjects.update', [$curriculum, $assignment]), [
            'subject_id' => $options['subject']->getKey(),
            'year_level_id' => $options['yearLevel']->getKey(),
            'academic_period_id' => $options['period']->getKey(),
            'sort_order' => 1,
            'prerequisite_ids' => [(string) $assignment->getKey()],
            'corequisite_ids' => [(string) $otherAssignment->getKey()],
        ])
        ->assertSessionHasErrors(['prerequisite_ids', 'corequisite_ids.0']);
});

test('deleting a curriculum also deletes assignments and dependency links', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin delete curricula');
    $options = curriculumOptions();
    $curriculum = createCurriculum($options);
    $first = $curriculum->curriculumSubjects()->create([
        'subject_id' => $options['subject']->getKey(),
        'year_level_id' => $options['yearLevel']->getKey(),
        'academic_period_id' => $options['period']->getKey(),
        'is_required' => true,
        'sort_order' => 1,
    ]);

    $this
        ->actingAs($user)
        ->delete(route('admin.academics.curriculum.destroy', $curriculum))
        ->assertRedirect(route('admin.academics.curriculum.index'));

    $this->assertModelMissing($curriculum);
    $this->assertModelMissing($first);
});

test('curriculum forms validate basic information and subject placement', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(['admin create curricula', 'admin update curricula']);
    $options = curriculumOptions();
    $curriculum = createCurriculum($options);
    $otherStructure = AcademicTermStructure::factory()->create();
    $wrongPeriod = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $otherStructure->getKey(),
    ]);

    $this
        ->actingAs($user)
        ->post(route('admin.academics.curriculum.store'), [
            'code' => '',
            'name' => '',
            'program_id' => 999,
            'academic_term_structure_id' => 999,
            'effective_year' => 1000,
            'number_of_years' => 0,
            'status' => 'Unknown',
        ])
        ->assertSessionHasErrors([
            'code',
            'name',
            'program_id',
            'academic_term_structure_id',
            'effective_year',
            'number_of_years',
            'status',
        ]);

    $this
        ->actingAs($user)
        ->post(route('admin.academics.curriculum.subjects.store', $curriculum), [
            'subject_id' => $options['subject']->getKey(),
            'year_level_id' => $options['yearLevel']->getKey(),
            'academic_period_id' => $wrongPeriod->getKey(),
            'sort_order' => -1,
        ])
        ->assertSessionHasErrors(['academic_period_id', 'sort_order']);
});

test('users without permission cannot create a curriculum or add subjects', function () {
    $user = User::factory()->create();
    $options = curriculumOptions();
    $curriculum = createCurriculum($options);

    $this
        ->actingAs($user)
        ->post(route('admin.academics.curriculum.store'), [
            'code' => 'UNAUTHORIZED',
            'name' => 'Unauthorized Curriculum',
            'program_id' => $options['program']->getKey(),
            'academic_term_structure_id' => $options['structure']->getKey(),
            'effective_year' => 2026,
            'number_of_years' => 4,
            'status' => 'Draft',
        ])
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->post(route('admin.academics.curriculum.subjects.store', $curriculum), [
            'subject_id' => $options['subject']->getKey(),
            'year_level_id' => $options['yearLevel']->getKey(),
            'academic_period_id' => $options['period']->getKey(),
            'sort_order' => 1,
        ])
        ->assertForbidden();
});

test('users without permission cannot open curriculum form pages', function () {
    $user = User::factory()->create();
    $options = curriculumOptions();
    $curriculum = createCurriculum($options);

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

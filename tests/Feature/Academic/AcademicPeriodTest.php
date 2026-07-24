<?php

use App\Enums\AcademicStatus;
use App\Enums\AcademicTermStructureType;
use App\Models\Academics\AcademicPeriod;
use App\Models\Academics\AcademicTermStructure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    foreach ([
        'admin create academic periods',
        'admin update academic periods',
        'admin delete academic periods',
    ] as $permission) {
        Permission::findOrCreate($permission, 'web');
    }
});

function academicPeriodPayload(
    AcademicTermStructure $structure,
    array $overrides = [],
): array {
    return [
        'academic_term_structure_id' => $structure->getKey(),
        'parent_id' => null,
        'name' => 'First Semester',
        'code' => 'SEM1',
        'sequence' => 1,
        'status' => AcademicStatus::Active->value,
        ...$overrides,
    ];
}

function academicPeriodUser(string $permission): User
{
    $user = User::factory()->create();
    $user->givePermissionTo($permission);

    return $user;
}

test('can create a root period', function () {
    $structure = AcademicTermStructure::factory()->create();

    $this
        ->actingAs(academicPeriodUser('admin create academic periods'))
        ->post(
            route('admin.academics.academic-periods.store'),
            academicPeriodPayload($structure),
        )
        ->assertRedirect(route('admin.academics.academic-term-structures.index'));

    $period = AcademicPeriod::query()->where('code', 'SEM1')->firstOrFail();

    expect($period->parent_id)->toBeNull();
});

test('can create a grading period under a semester', function () {
    $structure = AcademicTermStructure::factory()->create();
    $semester = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $structure->getKey(),
        'sequence' => 1,
    ]);

    $this
        ->actingAs(academicPeriodUser('admin create academic periods'))
        ->post(route('admin.academics.academic-periods.store'), academicPeriodPayload(
            $structure,
            [
                'parent_id' => $semester->getKey(),
                'name' => 'Prelim',
                'code' => 'PRE',
            ],
        ))
        ->assertRedirect(route('admin.academics.academic-term-structures.index'));

    expect($semester->children()->firstOrFail()->name)->toBe('Prelim');
});

test('can create root quarters without children', function () {
    $structure = AcademicTermStructure::factory()->quarterly()->create();

    foreach (range(1, 4) as $sequence) {
        $this
            ->actingAs(academicPeriodUser('admin create academic periods'))
            ->post(route('admin.academics.academic-periods.store'), academicPeriodPayload(
                $structure,
                [
                    'name' => "Quarter {$sequence}",
                    'code' => "Q{$sequence}",
                    'sequence' => $sequence,
                ],
            ))
            ->assertSessionHasNoErrors();
    }

    expect($structure->rootPeriods()->count())->toBe(4)
        ->and($structure->periods()->whereNotNull('parent_id')->count())->toBe(0);
});

test('cannot assign a parent from another structure', function () {
    $structure = AcademicTermStructure::factory()->create();
    $otherStructure = AcademicTermStructure::factory()->create();
    $foreignParent = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $otherStructure->getKey(),
        'sequence' => 1,
    ]);

    $this
        ->actingAs(academicPeriodUser('admin create academic periods'))
        ->post(route('admin.academics.academic-periods.store'), academicPeriodPayload(
            $structure,
            [
                'parent_id' => $foreignParent->getKey(),
                'name' => 'Prelim',
            ],
        ))
        ->assertSessionHasErrors('parent_id');
});

test('cannot assign a grading period as a parent or create a third level', function () {
    $structure = AcademicTermStructure::factory()->create();
    $root = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $structure->getKey(),
        'sequence' => 1,
    ]);
    $gradingPeriod = AcademicPeriod::factory()->childOf($root)->create([
        'name' => 'Prelim',
        'sequence' => 1,
    ]);

    $this
        ->actingAs(academicPeriodUser('admin create academic periods'))
        ->post(route('admin.academics.academic-periods.store'), academicPeriodPayload(
            $structure,
            [
                'parent_id' => $gradingPeriod->getKey(),
                'name' => 'Another Period',
            ],
        ))
        ->assertSessionHasErrors('parent_id');
});

test('cannot set itself as parent', function () {
    $structure = AcademicTermStructure::factory()->create();
    $period = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $structure->getKey(),
        'sequence' => 1,
    ]);

    $this
        ->actingAs(academicPeriodUser('admin update academic periods'))
        ->put(
            route('admin.academics.academic-periods.update', $period),
            academicPeriodPayload($structure, [
                'parent_id' => $period->getKey(),
                'name' => $period->name,
                'code' => $period->code,
            ]),
        )
        ->assertSessionHasErrors('parent_id');
});

test('cannot create duplicate sequence or name under the same parent', function () {
    $structure = AcademicTermStructure::factory()->create();
    AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $structure->getKey(),
        'name' => 'First Semester',
        'sequence' => 1,
    ]);
    $user = academicPeriodUser('admin create academic periods');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.academic-periods.store'), academicPeriodPayload(
            $structure,
            ['name' => 'Second Semester', 'sequence' => 1],
        ))
        ->assertSessionHasErrors('sequence');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.academic-periods.store'), academicPeriodPayload(
            $structure,
            ['name' => 'First Semester', 'sequence' => 2],
        ))
        ->assertSessionHasErrors('name');
});

test('can use the same sequence under different parents', function () {
    $structure = AcademicTermStructure::factory()->create();
    $firstSemester = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $structure->getKey(),
        'name' => 'First Semester',
        'sequence' => 1,
    ]);
    $secondSemester = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $structure->getKey(),
        'name' => 'Second Semester',
        'sequence' => 2,
    ]);
    $user = academicPeriodUser('admin create academic periods');

    foreach ([$firstSemester, $secondSemester] as $semester) {
        $this
            ->actingAs($user)
            ->post(route('admin.academics.academic-periods.store'), academicPeriodPayload(
                $structure,
                [
                    'parent_id' => $semester->getKey(),
                    'name' => 'Prelim',
                    'sequence' => 1,
                ],
            ))
            ->assertSessionHasNoErrors();
    }

    expect(AcademicPeriod::query()->where('name', 'Prelim')->count())->toBe(2);
});

test('deleting a root period deletes its children', function () {
    $structure = AcademicTermStructure::factory()->create();
    $root = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $structure->getKey(),
        'sequence' => 1,
    ]);
    $child = AcademicPeriod::factory()->childOf($root)->create(['sequence' => 1]);

    $this
        ->actingAs(academicPeriodUser('admin delete academic periods'))
        ->delete(route('admin.academics.academic-periods.destroy', $root))
        ->assertRedirect(route('admin.academics.academic-term-structures.index'));

    $this->assertModelMissing($root);
    $this->assertModelMissing($child);
});

test('quarterly structures reject grading periods', function () {
    $structure = AcademicTermStructure::factory()->quarterly()->create();
    $root = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $structure->getKey(),
        'sequence' => 1,
    ]);
    $user = academicPeriodUser('admin create academic periods');

    $this
        ->actingAs($user)
        ->post(route('admin.academics.academic-periods.store'), academicPeriodPayload(
            $structure,
            [
                'parent_id' => $root->getKey(),
                'name' => 'First Grading Period',
            ],
        ))
        ->assertSessionHasErrors('parent_id');
});

test('structures that allow grading periods enforce four children per root', function (
    AcademicTermStructureType $type,
) {
    $structure = AcademicTermStructure::factory()->create(['type' => $type]);
    $root = AcademicPeriod::factory()->create([
        'academic_term_structure_id' => $structure->getKey(),
        'sequence' => 1,
    ]);

    foreach (range(1, 4) as $sequence) {
        AcademicPeriod::factory()->childOf($root)->create([
            'name' => "Grading Period {$sequence}",
            'sequence' => $sequence,
        ]);
    }

    $this
        ->actingAs(academicPeriodUser('admin create academic periods'))
        ->post(route('admin.academics.academic-periods.store'), academicPeriodPayload(
            $structure,
            [
                'parent_id' => $root->getKey(),
                'name' => 'Fifth Grading Period',
                'code' => 'GP5',
                'sequence' => 5,
            ],
        ))
        ->assertSessionHasErrors('parent_id');

    expect($root->children()->count())->toBe(4);
})->with([
    AcademicTermStructureType::Semester,
    AcademicTermStructureType::Trisem,
    AcademicTermStructureType::Term,
]);

test('users without permission cannot manage academic periods', function () {
    $structure = AcademicTermStructure::factory()->create();

    $this
        ->actingAs(User::factory()->create())
        ->post(
            route('admin.academics.academic-periods.store'),
            academicPeriodPayload($structure),
        )
        ->assertForbidden();

    expect($structure->periods()->exists())->toBeFalse();
});

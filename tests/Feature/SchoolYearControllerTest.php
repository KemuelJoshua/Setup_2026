<?php

use App\Enums\SchoolYearStatus;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    foreach ([
        'admin view school-year',
        'admin create school-year',
        'admin update school-year',
        'admin delete school-year',
    ] as $permission) {
        Permission::create([
            'name' => $permission,
            'guard_name' => 'web',
        ]);
    }
});

test('authorized users can view the school years page', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin view school-year');

    SchoolYear::factory()->create([
        'sc_name' => 'School Year 2026-2027',
        'sc_code' => 'SY-2026',
        'sc_start_date' => '2026-06-01',
        'sc_end_date' => '2027-03-31',
        'sc_status' => SchoolYearStatus::ACTIVE,
    ]);

    $this
        ->actingAs($user)
        ->get(route('admin.school-years.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/school-years/Index')
            ->has('schoolYears.data')
            ->where('schoolYears.data.0.sc_name', 'School Year 2026-2027')
            ->where('schoolYears.data.0.sc_code', 'SY-2026')
            ->where('schoolYears.data.0.sc_status', SchoolYearStatus::ACTIVE->value));
});

test('authorized users can paginate and search school years', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin view school-year');

    SchoolYear::factory()->count(17)->create();
    SchoolYear::factory()->create([
        'sc_name' => 'Distinct Academic Year',
        'sc_code' => 'DISTINCT-2028',
    ]);

    $this
        ->actingAs($user)
        ->get(route('admin.school-years.index', [
            'per_page' => 10,
            'page' => 2,
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('schoolYears.current_page', 2)
            ->where('schoolYears.per_page', 10)
            ->where('schoolYears.total', 18)
            ->count('schoolYears.data', 8));

    $this
        ->actingAs($user)
        ->get(route('admin.school-years.index', [
            'search' => 'DISTINCT-2028',
            'per_page' => 10,
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('filters.search', 'DISTINCT-2028')
            ->where('schoolYears.total', 1)
            ->where('schoolYears.data.0.sc_name', 'Distinct Academic Year'));
});

test('authorized users can create a school year', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create school-year');

    $this
        ->actingAs($user)
        ->post(route('admin.school-years.store'), [
            'sc_name' => 'School Year 2027-2028',
            'sc_code' => 'SY-2027',
            'sc_start_date' => '2027-06-01',
            'sc_end_date' => '2028-03-31',
            'sc_status' => SchoolYearStatus::PLANNED->value,
        ])
        ->assertRedirect(route('admin.school-years.index'))
        ->assertSessionHas('success', 'School year created successfully.');

    $schoolYear = SchoolYear::where('sc_code', 'SY-2027')->firstOrFail();

    expect($schoolYear->sc_name)->toBe('School Year 2027-2028')
        ->and($schoolYear->sc_status)->toBe(SchoolYearStatus::PLANNED);
});

test('authorized users can update a school year', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin update school-year');
    $schoolYear = SchoolYear::factory()->planned()->create([
        'sc_code' => 'SY-2026',
    ]);

    $this
        ->actingAs($user)
        ->put(route('admin.school-years.update', $schoolYear), [
            'sc_name' => 'Updated School Year',
            'sc_code' => 'SY-2026-UPDATED',
            'sc_start_date' => '2026-07-01',
            'sc_end_date' => '2027-04-30',
            'sc_status' => SchoolYearStatus::ACTIVE->value,
        ])
        ->assertRedirect(route('admin.school-years.index'))
        ->assertSessionHas('success', 'School year updated successfully.');

    $schoolYear->refresh();

    expect($schoolYear->sc_name)->toBe('Updated School Year')
        ->and($schoolYear->sc_code)->toBe('SY-2026-UPDATED')
        ->and($schoolYear->sc_status)->toBe(SchoolYearStatus::ACTIVE);
});

test('authorized users can delete a school year', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin delete school-year');
    $schoolYear = SchoolYear::factory()->create();

    $this
        ->actingAs($user)
        ->delete(route('admin.school-years.destroy', $schoolYear))
        ->assertRedirect(route('admin.school-years.index'))
        ->assertSessionHas('success', 'School year deleted successfully.');

    $this->assertModelMissing($schoolYear);
});

test('school year forms validate required fields', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('admin create school-year');

    $this
        ->actingAs($user)
        ->from(route('admin.school-years.index'))
        ->post(route('admin.school-years.store'), [
            'sc_name' => '',
            'sc_code' => '',
            'sc_start_date' => '2027-06-01',
            'sc_end_date' => '2027-05-31',
            'sc_status' => 'archived',
        ])
        ->assertRedirect(route('admin.school-years.index'))
        ->assertSessionHasErrors(['sc_name', 'sc_code', 'sc_end_date', 'sc_status']);
});

test('users without permission cannot create a school year', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('admin.school-years.store'), [
            'sc_name' => 'School Year 2027-2028',
            'sc_code' => 'SY-2027',
            'sc_start_date' => '2027-06-01',
            'sc_end_date' => '2028-03-31',
            'sc_status' => SchoolYearStatus::PLANNED->value,
        ])
        ->assertForbidden();

    expect(SchoolYear::query()->where('sc_code', 'SY-2027')->exists())->toBeFalse();
});

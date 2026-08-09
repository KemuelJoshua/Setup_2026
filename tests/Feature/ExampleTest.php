<?php

use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('provides the welcome page visual assets', function () {
    expect(public_path('img/background.png'))->toBeFile()
        ->and(public_path('img/makati-deped.png'))->toBeFile()
        ->and(public_path('img/makati-logo.png'))->toBeFile();
});

test('keeps a legacy school address when structured fields are empty', function () {
    $school = Tenant::query()->forceCreate([
        'id' => 'legacy-school',
        'school_code' => 'LEGACY',
        'school_name' => 'Legacy School',
        'school_address' => 'Makati City',
    ]);

    expect($school->school_address)->toBe('Makati City');
});

test('shows active schools with a configured domain', function () {
    $category = Category::factory()->create(['name' => 'Elementary Schools']);
    Category::factory()->inactive()->create(['name' => 'Hidden Schools']);

    Event::fake();

    $activeSchool = Tenant::query()->forceCreate([
        'id' => 'active-school',
        'category_id' => $category->getKey(),
        'school_code' => 'ACTIVE',
        'school_name' => 'Active Learning Academy',
        'school_address_line_1' => '123 Learning Street',
        'school_barangay' => 'Barangay Uno',
        'school_city_municipality' => 'Makati City',
        'school_region' => 'NCR',
        'school_postal_code' => '1200',
        'school_motto' => 'Learn with purpose',
    ]);
    $activeSchool->domains()->create(['domain' => 'active-school.test']);

    $inactiveSchool = Tenant::query()->forceCreate([
        'id' => 'inactive-school',
        'school_code' => 'INACTIVE',
        'school_name' => 'Inactive School',
        'is_active' => false,
    ]);
    $inactiveSchool->domains()->create(['domain' => 'inactive-school.test']);

    Tenant::query()->forceCreate([
        'id' => 'school-without-domain',
        'school_code' => 'NO-DOMAIN',
        'school_name' => 'School Without Domain',
    ]);

    $response = $this->get(route('home'));

    $response->assertSuccessful()->assertInertia(fn (Assert $page) => $page
        ->component('Welcome')
        ->has('schools', 1)
        ->where('schools.0.id', $activeSchool->getTenantKey())
        ->where('schools.0.code', 'ACTIVE')
        ->where('schools.0.name', 'Active Learning Academy')
        ->where('schools.0.address', '123 Learning Street, Barangay Uno, Makati City, NCR, 1200')
        ->where('schools.0.motto', 'Learn with purpose')
        ->where('schools.0.url', 'http://active-school.test')
        ->has('categories', 1)
        ->where('categories.0.id', $category->getKey())
        ->where('categories.0.name', 'Elementary Schools')
        ->has('categories.0.schools', 1)
        ->where('categories.0.schools.0.id', $activeSchool->getTenantKey())
    );
});

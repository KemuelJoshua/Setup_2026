<?php

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('shows active schools with a configured domain', function () {
    Event::fake();

    $activeSchool = Tenant::query()->forceCreate([
        'id' => 'active-school',
        'school_code' => 'ACTIVE',
        'school_name' => 'Active Learning Academy',
        'school_address' => 'Makati City',
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
        ->where('schools.0.address', 'Makati City')
        ->where('schools.0.motto', 'Learn with purpose')
        ->where('schools.0.url', 'http://active-school.test')
    );
});

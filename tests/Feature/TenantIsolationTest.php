<?php

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\permissions\PermissionSeeder;
use Database\Seeders\permissions\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\PermissionRegistrar;

uses(RefreshDatabase::class);

afterEach(function (): void {
    if (tenancy()->initialized) {
        tenancy()->end();
    }

    Tenant::all()->each(function (Tenant $tenant): void {
        $databasePath = database_path($tenant->database()->getName());

        $tenant->delete();
        File::delete($databasePath);
    });
});

it('isolates users roles and permissions between tenant databases', function () {
    $firstTenant = Tenant::query()->create(['school_code' => 'A', 'school_name' => 'School A']);
    $firstTenant->domains()->create(['domain' => 'school-a.test']);

    $secondTenant = Tenant::query()->create(['school_code' => 'B', 'school_name' => 'School B']);
    $secondTenant->domains()->create(['domain' => 'school-b.test']);

    $firstTenant->run(function (): void {
        app(PermissionSeeder::class)->run();
        app(RoleSeeder::class)->run();

        User::factory()->create(['email' => 'admin@school-a.test'])->assignRole('School Admin');
    });

    $secondTenant->run(function (): void {
        app(PermissionSeeder::class)->run();
        app(RoleSeeder::class)->run();

        User::factory()->create(['email' => 'admin@school-b.test'])->assignRole('School Admin');
    });

    $firstTenant->run(function (): void {
        expect(User::query()->pluck('email')->all())->toBe(['admin@school-a.test']);
        expect(User::query()->firstOrFail()->hasRole('School Admin'))->toBeTrue();
        expect(app(PermissionRegistrar::class)->cacheKey)
            ->toBe('spatie.permission.cache.tenant.'.tenant('id'));
    });

    expect(app(PermissionRegistrar::class)->cacheKey)->toBe('spatie.permission.cache');

    $secondTenant->run(function (): void {
        expect(User::query()->pluck('email')->all())->toBe(['admin@school-b.test']);
        expect(User::query()->firstOrFail()->hasRole('School Admin'))->toBeTrue();
    });
});

it('keeps central and tenant routes in their own application areas', function () {
    $tenant = Tenant::query()->create(['school_code' => 'A', 'school_name' => 'School A']);
    $tenant->domains()->create(['domain' => 'school-a.test']);

    $this->get('http://school-a.test/login')->assertSuccessful();
    tenancy()->end();
    $this->get('http://school-a.test/central/tenants')->assertNotFound();
    tenancy()->end();
    $this->get('http://localhost/admin/users')->assertNotFound();
});

it('blocks an inactive school domain', function () {
    $tenant = Tenant::query()->create([
        'school_code' => 'I',
        'school_name' => 'Inactive School',
        'is_active' => false,
    ]);
    $tenant->domains()->create(['domain' => 'inactive.test']);

    $this->get('http://inactive.test/')->assertForbidden();
    $this->get('http://inactive.test/login')->assertForbidden();
});

it('sends central and tenant users to the correct dashboard after login', function () {
    $centralAdministrator = User::factory()->create(['email_verified_at' => now()]);

    $this->post('http://localhost/login', [
        'email' => $centralAdministrator->email,
        'password' => 'password',
    ])->assertRedirect('/central/tenants');

    $this->post('http://localhost/logout');

    $tenant = Tenant::query()->create([
        'school_code' => 'SCHOOL-A',
        'school_name' => 'School A',
    ]);
    $tenant->domains()->create(['domain' => 'school-a.test']);

    $tenant->run(function (): void {
        User::factory()->create([
            'email' => 'admin@school-a.test',
            'email_verified_at' => now(),
        ]);
    });

    $this->post('http://school-a.test/login', [
        'email' => 'admin@school-a.test',
        'password' => 'password',
    ])->assertRedirect('/admin/dashboard');

    tenancy()->end();

    $this->get('http://school-a.test/admin/dashboard')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('tenant.id', $tenant->getTenantKey()));
});

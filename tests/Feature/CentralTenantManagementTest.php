<?php

use App\Models\Category;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Inertia\Testing\AssertableInertia as Assert;
use Stancl\Tenancy\Events\TenantCreated;

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

it('provisions a school database with only authorization data and its first administrator', function () {
    $platformAdministrator = User::factory()->create(['email_verified_at' => now()]);
    $category = Category::factory()->create();
    $centralTransactionLevelBeforeProvisioning = DB::connection(config('tenancy.database.central_connection'))->transactionLevel();
    $centralTransactionLevel = null;

    Event::listen(TenantCreated::class, function () use (&$centralTransactionLevel): void {
        $centralTransactionLevel = DB::connection(config('tenancy.database.central_connection'))->transactionLevel();
    });

    $response = $this->actingAs($platformAdministrator)
        ->withServerVariables(['HTTP_HOST' => 'localhost'])
        ->post(route('central.tenants.store'), [
            'category_id' => $category->getKey(),
            'school_code' => 'SCHOOL-A',
            'school_name' => 'School A',
            'domain' => 'school-a.test',
            'is_active' => true,
            'admin_name' => 'School Administrator',
            'admin_email' => 'admin@school-a.test',
            'admin_password' => 'password',
            'admin_password_confirmation' => 'password',
        ]);

    $response->assertRedirect(route('central.tenants.index', absolute: false));

    $tenant = Tenant::query()->where('school_code', 'SCHOOL-A')->firstOrFail();

    expect($tenant->domains()->value('domain'))->toBe('school-a.test');
    expect($tenant->category->is($category))->toBeTrue();
    expect($centralTransactionLevel)->toBe($centralTransactionLevelBeforeProvisioning);

    $tenant->run(function (): void {
        $administrator = User::query()->where('email', 'admin@school-a.test')->firstOrFail();

        expect($administrator->hasRole('School Admin'))->toBeTrue()
            ->and(DB::table('roles')->count())->toBeGreaterThan(0)
            ->and(DB::table('permissions')->count())->toBeGreaterThan(0)
            ->and(DB::table('school_years')->count())->toBe(0);
    });
});

it('only deletes inactive schools', function () {
    $platformAdministrator = User::factory()->create(['email_verified_at' => now()]);
    $category = Category::factory()->create();
    $tenant = Tenant::query()->create([
        'id' => 'school-a',
        'category_id' => $category->getKey(),
        'school_code' => 'A',
        'school_name' => 'School A',
    ]);

    $this->actingAs($platformAdministrator)
        ->withServerVariables(['HTTP_HOST' => 'localhost'])
        ->delete(route('central.tenants.destroy', $tenant))
        ->assertSessionHasErrors('tenant');

    $tenant->update(['is_active' => false]);

    $this->actingAs($platformAdministrator)
        ->withServerVariables(['HTTP_HOST' => 'localhost'])
        ->delete(route('central.tenants.destroy', $tenant))
        ->assertRedirect(route('central.tenants.index', absolute: false));

    expect(Tenant::query()->whereKey($tenant->getKey())->exists())->toBeFalse();
});

it('searches and updates schools from the central tenant module', function () {
    $platformAdministrator = User::factory()->create(['email_verified_at' => now()]);
    $category = Category::factory()->create(['name' => 'College']);
    $tenant = Tenant::query()->create([
        'category_id' => $category->getKey(),
        'school_code' => 'SCHOOL-A',
        'school_name' => 'School A',
        'school_email' => 'office@school-a.test',
    ]);
    $tenant->domains()->create(['domain' => 'school-a.test']);

    $this->actingAs($platformAdministrator)
        ->withServerVariables(['HTTP_HOST' => 'localhost'])
        ->get(route('central.tenants.index', ['search' => 'school-a.test', 'per_page' => 10]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('central/tenants/Index')
            ->where('filters.search', 'school-a.test')
            ->where('filters.per_page', 10)
            ->where('categories.0.name', 'College')
            ->has('tenants.data', 1)
            ->where('tenants.data.0.id', $tenant->getTenantKey())
            ->where('tenants.data.0.category.name', 'College'));

    $this->actingAs($platformAdministrator)
        ->withServerVariables(['HTTP_HOST' => 'localhost'])
        ->patch(route('central.tenants.update', $tenant), [
            'category_id' => $category->getKey(),
            'school_code' => 'SCHOOL-A',
            'school_name' => 'Updated School A',
            'school_address_line_1' => '123 Learning Street',
            'school_address_line_2' => 'Education Village',
            'school_barangay' => 'Barangay Uno',
            'school_city_municipality' => 'Makati City',
            'school_province' => null,
            'school_region' => 'NCR',
            'school_postal_code' => '1200',
            'school_email' => 'office@school-a.test',
            'school_contact_number' => '09123456789',
            'school_motto' => 'Learn and serve',
            'school_website' => 'https://school-a.test',
            'school_director' => 'School Director',
            'domain' => ' UPDATED-SCHOOL-A.TEST ',
            'is_active' => true,
        ])
        ->assertRedirect(route('central.tenants.index', absolute: false));

    $this->actingAs($platformAdministrator)
        ->withServerVariables([
            'HTTP_HOST' => 'localhost:8000',
            'SERVER_PORT' => 8000,
        ])
        ->patch("http://localhost:8000/central/tenants/{$tenant->getRouteKey()}/status", [
            'is_active' => false,
        ])
        ->assertRedirect('/central/tenants');

    expect($tenant->refresh())
        ->school_name->toBe('Updated School A')
        ->school_address_line_1->toBe('123 Learning Street')
        ->school_address_line_2->toBe('Education Village')
        ->school_barangay->toBe('Barangay Uno')
        ->school_city_municipality->toBe('Makati City')
        ->school_region->toBe('NCR')
        ->school_postal_code->toBe('1200')
        ->school_address->toBe('123 Learning Street, Education Village, Barangay Uno, Makati City, NCR, 1200')
        ->is_active->toBeFalse()
        ->and($tenant->domains()->value('domain'))->toBe('updated-school-a.test');
});

it('rejects invalid and central tenant domains', function (string $domain) {
    $platformAdministrator = User::factory()->create(['email_verified_at' => now()]);
    $category = Category::factory()->create();

    $this->actingAs($platformAdministrator)
        ->withServerVariables(['HTTP_HOST' => 'localhost'])
        ->post(route('central.tenants.store'), [
            'category_id' => $category->getKey(),
            'school_code' => 'SCHOOL-A',
            'school_name' => 'School A',
            'domain' => $domain,
            'is_active' => true,
            'admin_name' => 'School Administrator',
            'admin_email' => 'admin@school-a.test',
            'admin_password' => 'password',
            'admin_password_confirmation' => 'password',
        ])
        ->assertSessionHasErrors('domain');

    expect(Tenant::query()->exists())->toBeFalse();
})->with([
    'central domain' => 'localhost',
    'URL instead of hostname' => 'https://school-a.test/login',
    'hostname with port' => 'school-a.test:8000',
]);

<?php

use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Middleware\InitializeTenancyByDomainOrCentral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Contracts\LoginResponse;
use Stancl\Tenancy\Middleware\ScopeSessions;

it('uses a central database queue and production-safe tenant seeding', function () {
    expect(config('queue.connections.database.connection'))
        ->toBe(config('tenancy.database.central_connection'))
        ->and(config('tenancy.seeder_parameters'))
        ->toMatchArray(['--class' => 'TenantDatabaseSeeder', '--force' => true]);
});

it('initializes tenancy for Fortify routes outside central domains', function () {
    expect(config('fortify.middleware'))
        ->toBe(['web', InitializeTenancyByDomainOrCentral::class, EnsureTenantIsActive::class]);
});

it('bypasses tenancy on central domains', function () {
    $centralDomain = config('tenancy.central_domains')[0];
    $request = Request::create("http://{$centralDomain}/login");

    $response = app(InitializeTenancyByDomainOrCentral::class)
        ->handle($request, fn () => response('central'));

    expect($response->getContent())->toBe('central')
        ->and(tenancy()->initialized)->toBeFalse();
});

it('scopes tenant sessions', function () {
    expect(config('session.domain'))->toBeNull()
        ->and(config('session.connection'))->toBeNull();

    expect(Route::getRoutes()->getByName('tenant.home')?->gatherMiddleware())
        ->toContain(InitializeTenancyByDomainOrCentral::class, ScopeSessions::class);
});

it('redirects central logins to the central tenant dashboard', function () {
    $request = Request::create('/login', 'POST', server: ['HTTP_ACCEPT' => 'application/json']);
    $response = app(LoginResponse::class)->toResponse($request);

    expect(json_decode((string) $response->getContent(), true))
        ->toMatchArray(['redirect' => '/central/tenants']);
});

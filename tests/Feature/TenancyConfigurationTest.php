<?php

use App\Http\Middleware\EnsureTenantIsActive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Contracts\LoginResponse;
use Stancl\Tenancy\Features\UniversalRoutes;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\ScopeSessions;

it('uses a central database queue and production-safe tenant seeding', function () {
    expect(config('queue.connections.database.connection'))
        ->toBe(config('tenancy.database.central_connection'))
        ->and(config('tenancy.seeder_parameters'))
        ->toMatchArray(['--class' => 'TenantDatabaseSeeder', '--force' => true]);
});

it('uses the documented universal route middleware for shared authentication', function () {
    expect(config('tenancy.features'))
        ->toContain(UniversalRoutes::class)
        ->and(config('fortify.middleware'))
        ->toContain('universal', InitializeTenancyByDomain::class, EnsureTenantIsActive::class);
});

it('scopes tenant sessions', function () {
    expect(config('session.domain'))->toBeNull()
        ->and(config('session.connection'))->toBeNull();

    expect(Route::getRoutes()->getByName('tenant.home')?->gatherMiddleware())
        ->toContain(InitializeTenancyByDomain::class, ScopeSessions::class);
});

it('redirects central logins to the central tenant dashboard', function () {
    $request = Request::create('/login', 'POST', server: ['HTTP_ACCEPT' => 'application/json']);
    $response = app(LoginResponse::class)->toResponse($request);

    expect(json_decode((string) $response->getContent(), true))
        ->toMatchArray(['redirect' => '/central/tenants']);
});

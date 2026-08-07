<?php

declare(strict_types=1);

use App\Http\Controllers\Academics\SchoolYearController;
use App\Http\Controllers\Admin\Users\UserController;
use App\Http\Middleware\EnsureTenantIsActive;
use Illuminate\Support\Facades\DB;
// use App\Http\Middleware\InitializeTenancyByDomainOrCentral;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\ScopeSessions;

Route::middleware([
    InitializeTenancyByDomain::class,
    'web',
    EnsureTenantIsActive::class,
    PreventAccessFromCentralDomains::class,
    ScopeSessions::class,
])->group(function () {
    Route::get('/', fn () => auth()->check()
        ? to_route('admin.dashboard')
        : to_route('login')
    )->name('tenant.home');

    Route::get('/tenant-auth-debug', function () {
        dd([
            'host' => request()->getHost(),
            'url' => request()->fullUrl(),
            'tenancy_initialized' => tenancy()->initialized,
            'tenant_id' => tenant('id'),
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'default_connection' => DB::getDefaultConnection(),
            'database' => DB::connection()->getDatabaseName(),
            'session_connection' => config('session.connection'),
        ]);
    });

    Route::middleware('auth')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::inertia('dashboard', 'Dashboard')->name('dashboard');

            Route::resource('users', UserController::class)
                ->only(['index', 'store', 'update', 'destroy']);

            Route::resource('school-years', SchoolYearController::class)
                ->only(['index', 'store', 'update', 'destroy']);

            require __DIR__.'/academics.php';
            require __DIR__.'/settings.php';
        });
});

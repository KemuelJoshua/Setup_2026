<?php

declare(strict_types=1);

use App\Http\Controllers\Academics\SchoolYearController;
use App\Http\Controllers\Admin\Users\UserController;
use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Middleware\InitializeTenancyByDomainOrCentral;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\ScopeSessions;

Route::middleware([
    'web',
    InitializeTenancyByDomainOrCentral::class,
    EnsureTenantIsActive::class,
    PreventAccessFromCentralDomains::class,
    ScopeSessions::class,
])->group(function () {
    Route::get('/', fn () => auth()->check()
        ? to_route('admin.dashboard')
        : to_route('login'))->name('tenant.home');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::inertia('dashboard', 'Dashboard')->name('dashboard');

        Route::resource('users', UserController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::resource('school-years', SchoolYearController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        require __DIR__.'/academics.php';
        require __DIR__.'/settings.php';
    });
});

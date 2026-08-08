<?php

use App\Http\Controllers\Central\TenantController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::domain(config('tenancy.central_domains')[0])
    ->group(function (): void {
        Route::get('/', WelcomeController::class)->name('home');

        Route::middleware(['auth'])
            ->prefix('central')
            ->name('central.')
            ->group(function (): void {
                Route::redirect('/dashboard', '/central/tenants')
                    ->name('dashboard');

                Route::patch('/tenants/{tenant}/status', [
                    TenantController::class,
                    'updateStatus',
                ])->name('tenants.update-status');

                Route::resource('/tenants', TenantController::class)
                    ->except(['create', 'show', 'edit']);
            });
    });

<?php

use App\Http\Controllers\Central\TenantController;
use Illuminate\Support\Facades\Route;

Route::domain(config('tenancy.central_domains')[0])->group(function (): void {
    Route::inertia('/', 'Welcome')->name('home');

    Route::middleware(['auth'])->prefix('central')->name('central.')->group(function () {
        Route::redirect('dashboard', '/central/tenants')->name('dashboard');
        Route::patch('tenants/{tenant}/status', [TenantController::class, 'updateStatus'])
            ->name('tenants.update-status');
        Route::resource('tenants', TenantController::class)->except(['create', 'show', 'edit']);
    });
});

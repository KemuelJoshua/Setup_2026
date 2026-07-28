<?php

use App\Http\Controllers\Admin\ProjectImpact\ProjectImpactController;
use App\Http\Controllers\Admin\StartupTracking\StartupTrackingController;
use App\Http\Controllers\Admin\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::inertia('dashboard', 'Dashboard')->name('dashboard');

        Route::resource('users', UserController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::post('startup-tracking/import', [StartupTrackingController::class, 'import'])
            ->name('startup-tracking.import');
        Route::resource('startup-tracking', StartupTrackingController::class)
            ->parameters(['startup-tracking' => 'startup_tracking'])
            ->only(['index', 'store', 'update', 'destroy']);

        Route::post('project-impact-tracking/import', [ProjectImpactController::class, 'import'])
            ->name('project-impact-tracking.import');
        Route::resource('project-impact-tracking', ProjectImpactController::class)
            ->parameters(['project-impact-tracking' => 'project_impact'])
            ->only(['index', 'store', 'update', 'destroy']);

        require __DIR__.'/settings.php';
    });

});

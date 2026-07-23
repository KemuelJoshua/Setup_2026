<?php

use App\Http\Controllers\Academics\SchoolYearController;
use App\Http\Controllers\Admin\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

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

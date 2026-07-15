<?php

use App\Http\Controllers\Admin\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::inertia('dashboard', 'Dashboard')->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');

        require __DIR__.'/settings.php';
    });

});

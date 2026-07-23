<?php

use App\Http\Controllers\Academics\SemesterController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('academics')->name('academics.')->group(function () {

        Route::prefix('semester')->name('semester.')->group(function () {
            Route::get('/', [SemesterController::class, 'index'])->name('index');
            Route::post('/', [SemesterController::class, 'store'])->name('store');
            Route::put('/{semester}', [SemesterController::class, 'update'])->name('update');
            Route::delete('/{semester}', [SemesterController::class, 'destroy'])->name('destroy');
        });

    });
});

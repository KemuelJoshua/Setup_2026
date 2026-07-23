<?php

use App\Http\Controllers\Academics\GradeLevelController;
use App\Http\Controllers\Academics\SectionController;
use App\Http\Controllers\Academics\SemesterController;
use App\Http\Controllers\Academics\SubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('academics')->name('academics.')->group(function () {

        Route::prefix('semester')->name('semester.')->group(function () {
            Route::get('/', [SemesterController::class, 'index'])->name('index');
            Route::post('/', [SemesterController::class, 'store'])->name('store');
            Route::put('/{semester}', [SemesterController::class, 'update'])->name('update');
            Route::delete('/{semester}', [SemesterController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('grade-level')->name('grade-level.')->group(function () {
            Route::get('/', [GradeLevelController::class, 'index'])->name('index');
            Route::post('/', [GradeLevelController::class, 'store'])->name('store');
            Route::put('/{gradeLevel}', [GradeLevelController::class, 'update'])->name('update');
            Route::delete('/{gradeLevel}', [GradeLevelController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('section')->name('section.')->group(function () {
            Route::get('/', [SectionController::class, 'index'])->name('index');
            Route::post('/', [SectionController::class, 'store'])->name('store');
            Route::put('/{section}', [SectionController::class, 'update'])->name('update');
            Route::delete('/{section}', [SectionController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('subject')->name('subject.')->group(function () {
            Route::get('/', [SubjectController::class, 'index'])->name('index');
            Route::post('/', [SubjectController::class, 'store'])->name('store');
            Route::put('/{subject}', [SubjectController::class, 'update'])->name('update');
            Route::delete('/{subject}', [SubjectController::class, 'destroy'])->name('destroy');
        });

    });
});

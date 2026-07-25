<?php

use App\Http\Controllers\Academics\AcademicPeriodController;
use App\Http\Controllers\Academics\AcademicTermStructureController;
use App\Http\Controllers\Academics\CurriculumController;
use App\Http\Controllers\Academics\CurriculumSubjectController;
use App\Http\Controllers\Academics\EducationalLevelController;
use App\Http\Controllers\Academics\GradeLevelController;
use App\Http\Controllers\Academics\ProgramController;
use App\Http\Controllers\Academics\SectionController;
use App\Http\Controllers\Academics\SubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('academics')->name('academics.')->group(function () {

        Route::resource('academic-term-structures', AcademicTermStructureController::class)
            ->only(['index', 'store', 'update', 'destroy']);
        Route::resource('academic-periods', AcademicPeriodController::class)
            ->only(['store', 'update', 'destroy']);

        Route::prefix('educational-level')->name('educational-level.')->group(function () {
            Route::get('/', [EducationalLevelController::class, 'index'])->name('index');
            Route::post('/', [EducationalLevelController::class, 'store'])->name('store');
            Route::put('/{educationalLevel}', [EducationalLevelController::class, 'update'])->name('update');
            Route::delete('/{educationalLevel}', [EducationalLevelController::class, 'destroy'])->name('destroy');
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

        Route::prefix('program')->name('program.')->group(function () {
            Route::get('/', [ProgramController::class, 'index'])->name('index');
            Route::post('/', [ProgramController::class, 'store'])->name('store');
            Route::put('/{program}', [ProgramController::class, 'update'])->name('update');
            Route::delete('/{program}', [ProgramController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('curriculum')->name('curriculum.')->group(function () {
            Route::get('/', [CurriculumController::class, 'index'])->name('index');
            Route::get('/create', [CurriculumController::class, 'create'])->name('create');
            Route::post('/', [CurriculumController::class, 'store'])->name('store');
            Route::get('/{curriculum}/edit', [CurriculumController::class, 'edit'])->name('edit');
            Route::put('/{curriculum}', [CurriculumController::class, 'update'])->name('update');
            Route::patch('/{curriculum}/status', [CurriculumController::class, 'updateStatus'])
                ->name('update-status');
            Route::delete('/{curriculum}', [CurriculumController::class, 'destroy'])->name('destroy');
            Route::post('/{curriculum}/subjects', [CurriculumSubjectController::class, 'store'])
                ->name('subjects.store');
            Route::put('/{curriculum}/subjects/{curriculumSubject}', [CurriculumSubjectController::class, 'update'])
                ->name('subjects.update');
            Route::delete('/{curriculum}/subjects/{curriculumSubject}', [CurriculumSubjectController::class, 'destroy'])
                ->name('subjects.destroy');
        });

    });
});

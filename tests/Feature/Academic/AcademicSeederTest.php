<?php

use App\Models\Academics\AcademicTerm;
use App\Models\Academics\Curriculum;
use App\Models\Academics\CurriculumSubject;
use App\Models\Academics\GradeLevel;
use App\Models\Academics\GradingPeriod;
use App\Models\Academics\Program;
use App\Models\Academics\Section;
use App\Models\Academics\Subject;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('database seeder creates a complete repeatable academic dataset', function () {
    AcademicTerm::query()->create([
        'name' => 'Semester Track',
        'code' => 'SEM',
        'type' => 'Semester',
    ]);
    AcademicTerm::query()->create([
        'name' => 'Old Prelim',
        'code' => '1ST-PRE',
        'type' => 'Not Applicable',
    ]);

    $this->seed();
    $this->seed();

    expect(SchoolYear::query()->count())->toBe(3)
        ->and(AcademicTerm::query()->count())->toBe(7)
        ->and(GradingPeriod::query()->count())->toBe(8)
        ->and(GradeLevel::query()->count())->toBe(6)
        ->and(Section::query()->count())->toBe(3)
        ->and(Subject::query()->count())->toBe(8)
        ->and(Program::query()->count())->toBe(5)
        ->and(Curriculum::query()->count())->toBe(1)
        ->and(CurriculumSubject::query()->count())->toBe(32)
        ->and(User::query()->where('email', 'admin@gmail.com')->count())->toBe(1);

    expect(CurriculumSubject::query()->where('is_required', true)->count())
        ->toBe(32);

    $firstSemester = AcademicTerm::query()->where('code', '1ST')->firstOrFail();
    $secondSemester = AcademicTerm::query()->where('code', '2ND')->firstOrFail();

    expect($firstSemester->gradingPeriods()->pluck('code')->all())
        ->toBe(['PRE', 'MID', 'SEMI', 'FIN'])
        ->and($secondSemester->gradingPeriods()->pluck('code')->all())
        ->toBe(['PRE', 'MID', 'SEMI', 'FIN']);
});

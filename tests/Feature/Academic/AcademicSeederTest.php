<?php

use App\Models\Academics\Curriculum;
use App\Models\Academics\CurriculumSubject;
use App\Models\Academics\GradeLevel;
use App\Models\Academics\Program;
use App\Models\Academics\Section;
use App\Models\Academics\Semester;
use App\Models\Academics\Subject;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('database seeder creates a complete repeatable academic dataset', function () {
    $this->seed();
    $this->seed();

    expect(SchoolYear::query()->count())->toBe(3)
        ->and(Semester::query()->count())->toBe(2)
        ->and(GradeLevel::query()->count())->toBe(6)
        ->and(Section::query()->count())->toBe(3)
        ->and(Subject::query()->count())->toBe(8)
        ->and(Program::query()->count())->toBe(5)
        ->and(Curriculum::query()->count())->toBe(1)
        ->and(CurriculumSubject::query()->count())->toBe(16)
        ->and(User::query()->where('email', 'admin@gmail.com')->count())->toBe(1);

    expect(CurriculumSubject::query()->where('is_required', true)->count())
        ->toBe(16);
});

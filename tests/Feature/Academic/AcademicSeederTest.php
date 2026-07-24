<?php

use App\Enums\AcademicTermStructureType;
use App\Models\Academics\AcademicPeriod;
use App\Models\Academics\AcademicTermStructure;
use App\Models\Academics\Curriculum;
use App\Models\Academics\CurriculumSubject;
use App\Models\Academics\EducationalLevel;
use App\Models\Academics\GradeLevel;
use App\Models\Academics\Program;
use App\Models\Academics\Section;
use App\Models\Academics\Subject;
use App\Models\Academics\SchoolYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('database seeder creates a complete repeatable academic dataset', function () {
    $this->seed();
    $this->seed();

    expect(SchoolYear::query()->count())->toBe(3)
        ->and(AcademicTermStructure::query()->count())->toBe(6)
        ->and(AcademicPeriod::query()->count())->toBe(38)
        ->and(EducationalLevel::query()->count())->toBe(4)
        ->and(GradeLevel::query()->count())->toBe(16)
        ->and(Section::query()->count())->toBe(3)
        ->and(Subject::query()->count())->toBe(8)
        ->and(Program::query()->count())->toBe(6)
        ->and(Curriculum::query()->count())->toBe(1)
        ->and(CurriculumSubject::query()->count())->toBe(32)
        ->and(User::query()->where('email', 'admin@gmail.com')->count())->toBe(1);

    expect(GradeLevel::query()
        ->where('name', 'Grade 7')
        ->whereHas('educationalLevel', fn ($query) => $query
            ->where('name', 'Junior High School'))
        ->exists())->toBeTrue()
        ->and(Program::query()
            ->where('code', 'STEM')
            ->whereHas('educationalLevel', fn ($query) => $query
                ->where('name', 'Senior High School'))
            ->exists())->toBeTrue()
        ->and(Subject::query()
            ->whereHas('educationalLevel', fn ($query) => $query
                ->where('name', 'Junior High School'))
            ->count())->toBe(8);

    expect(CurriculumSubject::query()->where('is_required', true)->count())
        ->toBe(32);

    $structures = AcademicTermStructure::query()
        ->with('rootPeriods.children')
        ->whereIn('code', ['C24GP', 'C23GP', 'C34GP', 'C33GP', 'JHS4Q', 'SHS4Q'])
        ->get()
        ->keyBy('code');

    expect($structures['C24GP']->type)->toBe(AcademicTermStructureType::Semester)
        ->and($structures['C24GP']->rootPeriods)->toHaveCount(2)
        ->and($structures['C24GP']->rootPeriods->first()->children)->toHaveCount(4)
        ->and($structures['C23GP']->rootPeriods)->toHaveCount(2)
        ->and($structures['C23GP']->rootPeriods->first()->children)->toBeEmpty()
        ->and($structures['C34GP']->type)->toBe(AcademicTermStructureType::Trisem)
        ->and($structures['C34GP']->rootPeriods)->toHaveCount(3)
        ->and($structures['C34GP']->rootPeriods->first()->children)->toHaveCount(4)
        ->and($structures['C33GP']->rootPeriods)->toHaveCount(3)
        ->and($structures['C33GP']->rootPeriods->first()->children)->toBeEmpty()
        ->and($structures['JHS4Q']->type)->toBe(AcademicTermStructureType::Quarterly)
        ->and($structures['JHS4Q']->rootPeriods)->toHaveCount(4)
        ->and($structures['JHS4Q']->rootPeriods->first()->children)->toBeEmpty()
        ->and($structures['SHS4Q']->type)->toBe(AcademicTermStructureType::Quarterly)
        ->and($structures['SHS4Q']->rootPeriods)->toHaveCount(4)
        ->and($structures['SHS4Q']->rootPeriods->first()->children)->toBeEmpty();
});

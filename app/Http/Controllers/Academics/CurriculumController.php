<?php

namespace App\Http\Controllers\Academics;

use App\Actions\Academics\Curriculum\CreateCurriculumAction;
use App\Actions\Academics\Curriculum\DeleteCurriculumAction;
use App\Actions\Academics\Curriculum\GetCurriculumFormOptionsAction;
use App\Actions\Academics\Curriculum\IndexCurriculumAction;
use App\Actions\Academics\Curriculum\UpdateCurriculumAction;
use App\Actions\Academics\Curriculum\UpdateCurriculumStatusAction;
use App\Actions\Academics\EducationalLevel\ListEducationalLevelOptionsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreCurriculumRequest;
use App\Http\Requests\Academics\UpdateCurriculumRequest;
use App\Http\Requests\Academics\UpdateCurriculumStatusRequest;
use App\Models\Academics\Curriculum;
use App\Models\Academics\CurriculumSubject;
use App\Models\Academics\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CurriculumController extends Controller
{
    public function index(
        Request $request,
        IndexCurriculumAction $indexCurricula,
        ListEducationalLevelOptionsAction $listEducationalLevelOptions,
    ): Response {
        Gate::authorize('admin view curricula');

        $perPage = $request->integer('per_page', 15);
        $status = $request->string('status')->toString();

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'educational_level_id' => $request->integer('educational_level_id') ?: null,
            'program_id' => $request->integer('program_id') ?: null,
            'status' => in_array($status, ['Draft', 'Active', 'Inactive'], true)
                ? $status
                : null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        $curricula = $indexCurricula
            ->execute($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn (Curriculum $curriculum): array => [
                'id' => $curriculum->getKey(),
                'code' => $curriculum->code,
                'name' => $curriculum->name,
                'program' => $curriculum->program?->name,
                'academic_structure' => $curriculum->academicTermStructure?->name,
                'effective_year' => $curriculum->effective_year,
                'number_of_years' => $curriculum->number_of_years,
                'description' => $curriculum->description,
                'status' => $curriculum->status,
                'curriculum_subjects_count' => $curriculum->curriculum_subjects_count,
                'created_at' => $curriculum->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('admin/academics/curricula/Index', [
            'curricula' => $curricula,
            'filters' => $filters,
            'educationalLevels' => $listEducationalLevelOptions->execute(),
            'programs' => Program::query()
                ->orderBy('name')
                ->get(['id', 'educational_level_id', 'code', 'name']),
        ]);
    }

    public function create(GetCurriculumFormOptionsAction $getFormOptions): Response
    {
        Gate::authorize('admin create curricula');

        return Inertia::render('admin/academics/curricula/Form', [
            'curriculum' => null,
            ...$getFormOptions->execute(),
        ]);
    }

    public function store(
        StoreCurriculumRequest $request,
        CreateCurriculumAction $createCurriculum,
    ): RedirectResponse {
        Gate::authorize('admin create curricula');

        $curriculum = $createCurriculum->execute($request->validated());

        return redirect()
            ->route('admin.academics.curriculum.edit', $curriculum)
            ->with('success', 'Basic curriculum information saved. Add its subjects next.');
    }

    public function edit(
        Curriculum $curriculum,
        GetCurriculumFormOptionsAction $getFormOptions,
    ): Response {
        Gate::authorize('admin update curricula');

        $curriculum->load([
            'program:id,code,name',
            'academicTermStructure:id,name,code,type',
            'academicTermStructure.rootPeriods:id,academic_term_structure_id,name,code,sequence',
            'curriculumSubjects.subject:id,name',
            'curriculumSubjects.yearLevel:id,name',
            'curriculumSubjects.academicPeriod:id,name,code',
            'curriculumSubjects.prerequisites.subject:id,name',
            'curriculumSubjects.corequisites.subject:id,name',
        ]);

        return Inertia::render('admin/academics/curricula/Subjects', [
            'curriculum' => [
                'id' => $curriculum->getKey(),
                'code' => $curriculum->code,
                'name' => $curriculum->name,
                'program' => $curriculum->program,
                'academic_structure' => $curriculum->academicTermStructure,
                'effective_year' => $curriculum->effective_year,
                'number_of_years' => $curriculum->number_of_years,
                'description' => $curriculum->description,
                'status' => $curriculum->status,
                'curriculum_subjects' => $curriculum->curriculumSubjects
                    ->map(fn (CurriculumSubject $curriculumSubject): array => [
                        'id' => $curriculumSubject->getKey(),
                        'subject_id' => $curriculumSubject->subject_id,
                        'subject_name' => $curriculumSubject->subject->name,
                        'year_level_id' => $curriculumSubject->year_level_id,
                        'year_level_name' => $curriculumSubject->yearLevel->name,
                        'academic_period_id' => $curriculumSubject->academic_period_id,
                        'academic_period_name' => $curriculumSubject->academicPeriod->name,
                        'units' => $curriculumSubject->units,
                        'lecture_hours' => $curriculumSubject->lecture_hours,
                        'laboratory_hours' => $curriculumSubject->laboratory_hours,
                        'sort_order' => $curriculumSubject->sort_order,
                        'remarks' => $curriculumSubject->remarks,
                        'prerequisites' => $curriculumSubject->prerequisites
                            ->map(fn (CurriculumSubject $prerequisite): array => [
                                'id' => $prerequisite->getKey(),
                                'name' => $prerequisite->subject->name,
                            ])->all(),
                        'corequisites' => $curriculumSubject->corequisites
                            ->map(fn (CurriculumSubject $corequisite): array => [
                                'id' => $corequisite->getKey(),
                                'name' => $corequisite->subject->name,
                            ])->all(),
                    ])->all(),
            ],
            ...$getFormOptions->execute(),
        ]);
    }

    public function update(
        UpdateCurriculumRequest $request,
        Curriculum $curriculum,
        UpdateCurriculumAction $updateCurriculum,
    ): RedirectResponse {
        Gate::authorize('admin update curricula');

        $updateCurriculum->execute($curriculum, $request->validated());

        return redirect()
            ->route('admin.academics.curriculum.index')
            ->with('success', 'Curriculum updated successfully.');
    }

    public function updateStatus(
        UpdateCurriculumStatusRequest $request,
        Curriculum $curriculum,
        UpdateCurriculumStatusAction $updateCurriculumStatus,
    ): RedirectResponse {
        Gate::authorize('admin update curricula');

        $updateCurriculumStatus->execute(
            $curriculum,
            $request->string('status')->toString(),
        );

        return back()->with('success', 'Curriculum status updated successfully.');
    }

    public function destroy(
        Curriculum $curriculum,
        DeleteCurriculumAction $deleteCurriculum,
    ): RedirectResponse {
        Gate::authorize('admin delete curricula');

        $deleteCurriculum->execute($curriculum);

        return redirect()
            ->route('admin.academics.curriculum.index')
            ->with('success', 'Curriculum deleted successfully.');
    }
}

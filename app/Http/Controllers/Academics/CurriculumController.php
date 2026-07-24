<?php

namespace App\Http\Controllers\Academics;

use App\Actions\Academics\Curriculum\CreateCurriculumAction;
use App\Actions\Academics\Curriculum\DeleteCurriculumAction;
use App\Actions\Academics\Curriculum\GetCurriculumFormOptionsAction;
use App\Actions\Academics\Curriculum\IndexCurriculumAction;
use App\Actions\Academics\Curriculum\UpdateCurriculumAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreCurriculumRequest;
use App\Http\Requests\Academics\UpdateCurriculumRequest;
use App\Models\Academics\Curriculum;
use App\Models\Academics\CurriculumSubject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CurriculumController extends Controller
{
    public function index(Request $request, IndexCurriculumAction $indexCurricula): Response
    {
        Gate::authorize('admin view curricula');

        $perPage = $request->integer('per_page', 15);

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
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
                'effective_year' => $curriculum->effective_year,
                'description' => $curriculum->description,
                'status' => $curriculum->status,
                'curriculum_subjects_count' => $curriculum->curriculum_subjects_count,
                'created_at' => $curriculum->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('admin/academics/curricula/Index', [
            'curricula' => $curricula,
            'filters' => $filters,
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

        $createCurriculum->execute($request->validated());

        return redirect()
            ->route('admin.academics.curriculum.index')
            ->with('success', 'Curriculum created successfully.');
    }

    public function edit(
        Curriculum $curriculum,
        GetCurriculumFormOptionsAction $getFormOptions,
    ): Response {
        Gate::authorize('admin update curricula');

        $curriculum->load([
            'curriculumSubjects:id,curriculum_id,subject_id,year_level_id,academic_term_id,is_required,sort_order',
        ]);

        return Inertia::render('admin/academics/curricula/Form', [
            'curriculum' => [
                'id' => $curriculum->getKey(),
                'code' => $curriculum->code,
                'name' => $curriculum->name,
                'effective_year' => $curriculum->effective_year,
                'description' => $curriculum->description,
                'status' => $curriculum->status,
                'curriculum_subjects' => $curriculum->curriculumSubjects
                    ->map(fn (CurriculumSubject $curriculumSubject): array => [
                        'id' => $curriculumSubject->getKey(),
                        'subject_id' => $curriculumSubject->subject_id,
                        'year_level_id' => $curriculumSubject->year_level_id,
                        'academic_term_id' => $curriculumSubject->academic_term_id,
                        'is_required' => $curriculumSubject->is_required,
                        'sort_order' => $curriculumSubject->sort_order,
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

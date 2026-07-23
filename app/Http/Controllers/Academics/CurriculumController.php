<?php

namespace App\Http\Controllers\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreCurriculumRequest;
use App\Http\Requests\Academics\UpdateCurriculumRequest;
use App\Models\Academics\AcademicTerm;
use App\Models\Academics\Curriculum;
use App\Models\Academics\CurriculumSubject;
use App\Models\Academics\GradeLevel;
use App\Models\Academics\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CurriculumController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('admin view curricula');

        $perPage = $request->integer('per_page', 15);

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        $curricula = Curriculum::query()
            ->withCount('curriculumSubjects')
            ->when($filters['search'], function (Builder $query) use ($filters) {
                $search = $filters['search'];

                $query->where(function (Builder $query) use ($search) {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('effective_year', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->latest()
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

    public function create(): Response
    {
        Gate::authorize('admin create curricula');

        return Inertia::render('admin/academics/curricula/Form', [
            'curriculum' => null,
            ...$this->formOptions(),
        ]);
    }

    public function store(StoreCurriculumRequest $request): RedirectResponse
    {
        Gate::authorize('admin create curricula');

        $data = $request->validated();
        $curriculumSubjects = $data['curriculum_subjects'] ?? [];
        unset($data['curriculum_subjects']);

        DB::transaction(function () use ($data, $curriculumSubjects): void {
            $curriculum = Curriculum::query()->create($data);
            $curriculum->curriculumSubjects()->createMany($curriculumSubjects);
        });

        return redirect()
            ->route('admin.academics.curriculum.index')
            ->with('success', 'Curriculum created successfully.');
    }

    public function edit(Curriculum $curriculum): Response
    {
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
            ...$this->formOptions(),
        ]);
    }

    public function update(
        UpdateCurriculumRequest $request,
        Curriculum $curriculum,
    ): RedirectResponse {
        Gate::authorize('admin update curricula');

        $data = $request->validated();
        $curriculumSubjects = $data['curriculum_subjects'] ?? [];
        unset($data['curriculum_subjects']);

        DB::transaction(function () use ($curriculum, $data, $curriculumSubjects): void {
            $curriculum->update($data);
            $curriculum->curriculumSubjects()->delete();
            $curriculum->curriculumSubjects()->createMany($curriculumSubjects);
        });

        return redirect()
            ->route('admin.academics.curriculum.index')
            ->with('success', 'Curriculum updated successfully.');
    }

    public function destroy(Curriculum $curriculum): RedirectResponse
    {
        Gate::authorize('admin delete curricula');

        $curriculum->delete();

        return redirect()
            ->route('admin.academics.curriculum.index')
            ->with('success', 'Curriculum deleted successfully.');
    }

    /**
     * @return array{
     *     subjects: Collection<int, Subject>,
     *     yearLevels: Collection<int, GradeLevel>,
     *     academicTerms: Collection<int, AcademicTerm>
     * }
     */
    private function formOptions(): array
    {
        return [
            'subjects' => Subject::query()->orderBy('name')->get(['id', 'name']),
            'yearLevels' => GradeLevel::query()->orderBy('name')->get(['id', 'name']),
            'academicTerms' => AcademicTerm::query()
                ->orderBy('type')
                ->orderBy('name')
                ->get(['id', 'name', 'code', 'type']),
        ];
    }
}

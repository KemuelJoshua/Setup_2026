<?php

namespace App\Http\Controllers\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreAcademicTermRequest;
use App\Http\Requests\Academics\UpdateAcademicTermRequest;
use App\Models\Academics\AcademicTerm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AcademicTermController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('admin view academic terms');

        $perPage = $request->integer('per_page', 25);

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 25,
        ];

        $academicTerms = AcademicTerm::query()
            ->with('gradingPeriods:id,academic_term_id,name,code,sort_order')
            ->when($filters['search'], function (Builder $query) use ($filters) {
                $search = $filters['search'];

                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%");
                });
            })
            ->orderBy('id')
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn (AcademicTerm $academicTerm): array => [
                'id' => $academicTerm->getKey(),
                'name' => $academicTerm->name,
                'code' => $academicTerm->code,
                'type' => $academicTerm->type,
                'grading_periods' => $academicTerm->gradingPeriods
                    ->map(fn ($gradingPeriod): array => [
                        'id' => $gradingPeriod->getKey(),
                        'name' => $gradingPeriod->name,
                        'code' => $gradingPeriod->code,
                        'sort_order' => $gradingPeriod->sort_order,
                    ])
                    ->values(),
            ]);

        return Inertia::render('admin/academics/academic-terms/Index', [
            'academicTerms' => $academicTerms,
            'filters' => $filters,
        ]);
    }

    public function store(StoreAcademicTermRequest $request): RedirectResponse
    {
        Gate::authorize('admin create academic terms');

        $validated = $request->validated();
        $gradingPeriods = $validated['grading_periods'] ?? [];
        unset($validated['grading_periods']);

        DB::transaction(function () use ($validated, $gradingPeriods): void {
            $academicTerm = AcademicTerm::query()->create($validated);
            $academicTerm->gradingPeriods()->createMany($gradingPeriods);
        });

        return redirect()
            ->route('admin.academics.academic-term.index')
            ->with('success', 'Academic term created successfully.');
    }

    public function update(
        UpdateAcademicTermRequest $request,
        AcademicTerm $academicTerm,
    ): RedirectResponse {
        Gate::authorize('admin update academic terms');

        $validated = $request->validated();
        $gradingPeriods = $validated['grading_periods'] ?? [];
        unset($validated['grading_periods']);

        DB::transaction(function () use ($academicTerm, $validated, $gradingPeriods): void {
            $academicTerm->update($validated);
            $academicTerm->gradingPeriods()->delete();
            $academicTerm->gradingPeriods()->createMany($gradingPeriods);
        });

        return redirect()
            ->route('admin.academics.academic-term.index')
            ->with('success', 'Academic term updated successfully.');
    }

    public function destroy(AcademicTerm $academicTerm): RedirectResponse
    {
        Gate::authorize('admin delete academic terms');

        if ($academicTerm->curriculumSubjects()->exists()) {
            throw ValidationException::withMessages([
                'academic_term' => 'This academic term is assigned to a curriculum.',
            ]);
        }

        $academicTerm->delete();

        return redirect()
            ->route('admin.academics.academic-term.index')
            ->with('success', 'Academic term deleted successfully.');
    }
}

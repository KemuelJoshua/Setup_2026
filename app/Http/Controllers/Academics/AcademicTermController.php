<?php

namespace App\Http\Controllers\Academics;

use App\Actions\Academics\AcademicTerm\CreateAcademicTermAction;
use App\Actions\Academics\AcademicTerm\DeleteAcademicTermAction;
use App\Actions\Academics\AcademicTerm\IndexAcademicTermAction;
use App\Actions\Academics\AcademicTerm\UpdateAcademicTermAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreAcademicTermRequest;
use App\Http\Requests\Academics\UpdateAcademicTermRequest;
use App\Models\Academics\AcademicTerm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class AcademicTermController extends Controller
{
    public function index(Request $request, IndexAcademicTermAction $indexAcademicTerms): Response
    {
        Gate::authorize('admin view academic terms');

        $perPage = $request->integer('per_page', 25);

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 25,
        ];

        $academicTerms = $indexAcademicTerms
            ->execute($filters)
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

    public function store(
        StoreAcademicTermRequest $request,
        CreateAcademicTermAction $createAcademicTerm,
    ): RedirectResponse {
        Gate::authorize('admin create academic terms');

        $createAcademicTerm->execute($request->validated());

        return redirect()
            ->route('admin.academics.academic-term.index')
            ->with('success', 'Academic term created successfully.');
    }

    public function update(
        UpdateAcademicTermRequest $request,
        AcademicTerm $academicTerm,
        UpdateAcademicTermAction $updateAcademicTerm,
    ): RedirectResponse {
        Gate::authorize('admin update academic terms');

        $updateAcademicTerm->execute($academicTerm, $request->validated());

        return redirect()
            ->route('admin.academics.academic-term.index')
            ->with('success', 'Academic term updated successfully.');
    }

    public function destroy(
        AcademicTerm $academicTerm,
        DeleteAcademicTermAction $deleteAcademicTerm,
    ): RedirectResponse {
        Gate::authorize('admin delete academic terms');

        $deleteAcademicTerm->execute($academicTerm);

        return redirect()
            ->route('admin.academics.academic-term.index')
            ->with('success', 'Academic term deleted successfully.');
    }
}

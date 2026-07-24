<?php

namespace App\Http\Controllers\Academics;

use App\Actions\Academics\SchoolYear\CreateSchoolYearAction;
use App\Actions\Academics\SchoolYear\DeleteSchoolYearAction;
use App\Actions\Academics\SchoolYear\IndexSchoolYearAction;
use App\Actions\Academics\SchoolYear\UpdateSchoolYearAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\SchoolYear\StoreSchoolYearRequest;
use App\Http\Requests\Academics\SchoolYear\UpdateSchoolYearRequest;
use App\Models\Academics\SchoolYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SchoolYearController extends Controller
{
    public function index(Request $request, IndexSchoolYearAction $action): Response
    {
        Gate::authorize('admin view school-year');

        $perPage = $request->integer('per_page', 15);

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        $schoolYears = $action->execute($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn (SchoolYear $schoolYear): array => [
                'id' => $schoolYear->getKey(),
                'sc_name' => $schoolYear->sc_name,
                'sc_code' => $schoolYear->sc_code,
                'sc_start_date' => $schoolYear->sc_start_date?->toDateString(),
                'sc_end_date' => $schoolYear->sc_end_date?->toDateString(),
                'sc_status' => $schoolYear->sc_status->value,
            ]);

        return Inertia::render('admin/academics/school-years/Index', [
            'schoolYears' => $schoolYears,
            'filters' => $filters,
        ]);
    }

    public function store(StoreSchoolYearRequest $request, CreateSchoolYearAction $createSchoolYear): RedirectResponse
    {
        Gate::authorize('admin create school-year');

        $createSchoolYear->execute($request->validated());

        return redirect()
            ->route('admin.school-years.index')
            ->with('success', 'School year created successfully.');
    }

    public function update(
        UpdateSchoolYearRequest $request,
        SchoolYear $schoolYear,
        UpdateSchoolYearAction $updateSchoolYear,
    ): RedirectResponse {
        Gate::authorize('admin update school-year');

        $updateSchoolYear->execute((string) $schoolYear->getKey(), $request->validated());

        return redirect()
            ->route('admin.school-years.index')
            ->with('success', 'School year updated successfully.');
    }

    public function destroy(SchoolYear $schoolYear, DeleteSchoolYearAction $deleteSchoolYear): RedirectResponse
    {
        Gate::authorize('admin delete school-year');

        $deleteSchoolYear->execute((string) $schoolYear->getKey());

        return redirect()
            ->route('admin.school-years.index')
            ->with('success', 'School year deleted successfully.');
    }
}

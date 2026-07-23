<?php

namespace App\Http\Controllers\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreSemesterRequest;
use App\Http\Requests\Academics\UpdateSemesterRequest;
use App\Models\Academics\Semester;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SemesterController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('admin view semesters');

        $perPage = $request->integer('per_page', 15);

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        $semesters = Semester::query()
            ->when($filters['search'], function (Builder $query) use ($filters) {
                $search = $filters['search'];

                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })->latest()
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn (Semester $semester): array => [
                'id' => $semester->getKey(),
                'name' => $semester->name,
                'code' => $semester->code,
                'start_date' => $semester->start_date?->toDateString(),
                'end_date' => $semester->end_date?->toDateString(),
            ]);

        return Inertia::render('admin/academics/semesters/Index', [
            'semesters' => $semesters,
            'filters' => $filters,
        ]);
    }

    public function store(StoreSemesterRequest $request): RedirectResponse
    {
        Gate::authorize('admin create semesters');

        Semester::query()->create($request->validated());

        return redirect()
            ->route('admin.academics.semester.index')
            ->with('success', 'Semester created successfully.');
    }

    public function update(UpdateSemesterRequest $request, Semester $semester): RedirectResponse
    {
        Gate::authorize('admin update semesters');

        $semester->update($request->validated());

        return redirect()
            ->route('admin.academics.semester.index')
            ->with('success', 'Semester updated successfully.');
    }

    public function destroy(Semester $semester): RedirectResponse
    {
        Gate::authorize('admin delete semesters');

        $semester->delete();

        return redirect()
            ->route('admin.academics.semester.index')
            ->with('success', 'Semester deleted successfully.');
    }
}

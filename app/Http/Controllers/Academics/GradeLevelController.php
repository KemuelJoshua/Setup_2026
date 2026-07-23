<?php

namespace App\Http\Controllers\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreGradeLevelRequest;
use App\Http\Requests\Academics\UpdateGradeLevelRequest;
use App\Models\Academics\GradeLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class GradeLevelController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('admin view grade levels');

        $perPage = $request->integer('per_page', 15);

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        $gradeLevels = GradeLevel::query()
            ->when($filters['search'], function (Builder $query) use ($filters) {
                $query->where('name', 'like', "%{$filters['search']}%");
            })
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn (GradeLevel $gradeLevel): array => [
                'id' => $gradeLevel->getKey(),
                'name' => $gradeLevel->name,
                'created_at' => $gradeLevel->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('admin/academics/grade-levels/Index', [
            'gradeLevels' => $gradeLevels,
            'filters' => $filters,
        ]);
    }

    public function store(StoreGradeLevelRequest $request): RedirectResponse
    {
        Gate::authorize('admin create grade levels');

        GradeLevel::query()->create($request->validated());

        return redirect()
            ->route('admin.academics.grade-level.index')
            ->with('success', 'Grade level created successfully.');
    }

    public function update(
        UpdateGradeLevelRequest $request,
        GradeLevel $gradeLevel,
    ): RedirectResponse {
        Gate::authorize('admin update grade levels');

        $gradeLevel->update($request->validated());

        return redirect()
            ->route('admin.academics.grade-level.index')
            ->with('success', 'Grade level updated successfully.');
    }

    public function destroy(GradeLevel $gradeLevel): RedirectResponse
    {
        Gate::authorize('admin delete grade levels');

        $gradeLevel->delete();

        return redirect()
            ->route('admin.academics.grade-level.index')
            ->with('success', 'Grade level deleted successfully.');
    }
}

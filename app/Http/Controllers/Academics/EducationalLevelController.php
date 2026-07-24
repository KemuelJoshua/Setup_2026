<?php

namespace App\Http\Controllers\Academics;

use App\Actions\Academics\EducationalLevel\CreateEducationalLevelAction;
use App\Actions\Academics\EducationalLevel\DeleteEducationalLevelAction;
use App\Actions\Academics\EducationalLevel\IndexEducationalLevelAction;
use App\Actions\Academics\EducationalLevel\UpdateEducationalLevelAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreEducationalLevelRequest;
use App\Http\Requests\Academics\UpdateEducationalLevelRequest;
use App\Models\Academics\EducationalLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class EducationalLevelController extends Controller
{
    public function index(Request $request, IndexEducationalLevelAction $indexEducationalLevels): Response
    {
        Gate::authorize('admin view educational levels');

        $perPage = $request->integer('per_page', 15);

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        $educationalLevels = $indexEducationalLevels
            ->execute($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn (EducationalLevel $educationalLevel): array => [
                'id' => $educationalLevel->getKey(),
                'name' => $educationalLevel->name,
                'created_at' => $educationalLevel->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('admin/academics/educational-levels/Index', [
            'educationalLevels' => $educationalLevels,
            'filters' => $filters,
        ]);
    }

    public function store(
        StoreEducationalLevelRequest $request,
        CreateEducationalLevelAction $createEducationalLevel,
    ): RedirectResponse {
        Gate::authorize('admin create educational levels');

        $createEducationalLevel->execute($request->validated());

        return redirect()
            ->route('admin.academics.educational-level.index')
            ->with('success', 'Educational level created successfully.');
    }

    public function update(
        UpdateEducationalLevelRequest $request,
        EducationalLevel $educationalLevel,
        UpdateEducationalLevelAction $updateEducationalLevel,
    ): RedirectResponse {
        Gate::authorize('admin update educational levels');

        $updateEducationalLevel->execute($educationalLevel, $request->validated());

        return redirect()
            ->route('admin.academics.educational-level.index')
            ->with('success', 'Educational level updated successfully.');
    }

    public function destroy(
        EducationalLevel $educationalLevel,
        DeleteEducationalLevelAction $deleteEducationalLevel,
    ): RedirectResponse {
        Gate::authorize('admin delete educational levels');

        $deleteEducationalLevel->execute($educationalLevel);

        return redirect()
            ->route('admin.academics.educational-level.index')
            ->with('success', 'Educational level deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Academics;

use App\Actions\Academics\EducationalLevel\ListEducationalLevelOptionsAction;
use App\Actions\Academics\GradeLevel\CreateGradeLevelAction;
use App\Actions\Academics\GradeLevel\DeleteGradeLevelAction;
use App\Actions\Academics\GradeLevel\IndexGradeLevelAction;
use App\Actions\Academics\GradeLevel\UpdateGradeLevelAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreGradeLevelRequest;
use App\Http\Requests\Academics\UpdateGradeLevelRequest;
use App\Models\Academics\GradeLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class GradeLevelController extends Controller
{
    public function index(
        Request $request,
        IndexGradeLevelAction $indexGradeLevels,
        ListEducationalLevelOptionsAction $listEducationalLevelOptions,
    ): Response {
        Gate::authorize('admin view grade levels');

        $perPage = $request->integer('per_page', 15);

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'educational_level_id' => $request->integer('educational_level_id') ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        $gradeLevels = $indexGradeLevels
            ->execute($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn (GradeLevel $gradeLevel): array => [
                'id' => $gradeLevel->getKey(),
                'name' => $gradeLevel->name,
                'educational_level_id' => $gradeLevel->educational_level_id,
                'educational_level' => $gradeLevel->educationalLevel
                    ? [
                        'id' => $gradeLevel->educationalLevel->getKey(),
                        'name' => $gradeLevel->educationalLevel->name,
                    ]
                    : null,
                'created_at' => $gradeLevel->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('admin/academics/grade-levels/Index', [
            'gradeLevels' => $gradeLevels,
            'filters' => $filters,
            'educationalLevels' => $listEducationalLevelOptions->execute(),
        ]);
    }

    public function store(
        StoreGradeLevelRequest $request,
        CreateGradeLevelAction $createGradeLevel,
    ): RedirectResponse {
        Gate::authorize('admin create grade levels');

        $createGradeLevel->execute($request->validated());

        return redirect()
            ->route('admin.academics.grade-level.index')
            ->with('success', 'Grade level created successfully.');
    }

    public function update(
        UpdateGradeLevelRequest $request,
        GradeLevel $gradeLevel,
        UpdateGradeLevelAction $updateGradeLevel,
    ): RedirectResponse {
        Gate::authorize('admin update grade levels');

        $updateGradeLevel->execute($gradeLevel, $request->validated());

        return redirect()
            ->route('admin.academics.grade-level.index')
            ->with('success', 'Grade level updated successfully.');
    }

    public function destroy(
        GradeLevel $gradeLevel,
        DeleteGradeLevelAction $deleteGradeLevel,
    ): RedirectResponse {
        Gate::authorize('admin delete grade levels');

        $deleteGradeLevel->execute($gradeLevel);

        return redirect()
            ->route('admin.academics.grade-level.index')
            ->with('success', 'Grade level deleted successfully.');
    }
}

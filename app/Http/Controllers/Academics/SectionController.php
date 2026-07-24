<?php

namespace App\Http\Controllers\Academics;

use App\Actions\Academics\EducationalLevel\ListEducationalLevelOptionsAction;
use App\Actions\Academics\Section\CreateSectionAction;
use App\Actions\Academics\Section\DeleteSectionAction;
use App\Actions\Academics\Section\IndexSectionAction;
use App\Actions\Academics\Section\UpdateSectionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreSectionRequest;
use App\Http\Requests\Academics\UpdateSectionRequest;
use App\Models\Academics\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SectionController extends Controller
{
    public function index(
        Request $request,
        IndexSectionAction $indexSections,
        ListEducationalLevelOptionsAction $listEducationalLevelOptions,
    ): Response {
        Gate::authorize('admin view sections');

        $perPage = $request->integer('per_page', 15);

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'educational_level_id' => $request->integer('educational_level_id') ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        $sections = $indexSections
            ->execute($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn (Section $section): array => [
                'id' => $section->getKey(),
                'name' => $section->name,
                'educational_level_id' => $section->educational_level_id,
                'educational_level' => $section->educationalLevel
                    ? [
                        'id' => $section->educationalLevel->getKey(),
                        'name' => $section->educationalLevel->name,
                    ]
                    : null,
                'created_at' => $section->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('admin/academics/sections/Index', [
            'sections' => $sections,
            'filters' => $filters,
            'educationalLevels' => $listEducationalLevelOptions->execute(),
        ]);
    }

    public function store(
        StoreSectionRequest $request,
        CreateSectionAction $createSection,
    ): RedirectResponse {
        Gate::authorize('admin create sections');

        $createSection->execute($request->validated());

        return redirect()
            ->route('admin.academics.section.index')
            ->with('success', 'Section created successfully.');
    }

    public function update(
        UpdateSectionRequest $request,
        Section $section,
        UpdateSectionAction $updateSection,
    ): RedirectResponse {
        Gate::authorize('admin update sections');

        $updateSection->execute($section, $request->validated());

        return redirect()
            ->route('admin.academics.section.index')
            ->with('success', 'Section updated successfully.');
    }

    public function destroy(
        Section $section,
        DeleteSectionAction $deleteSection,
    ): RedirectResponse {
        Gate::authorize('admin delete sections');

        $deleteSection->execute($section);

        return redirect()
            ->route('admin.academics.section.index')
            ->with('success', 'Section deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin\ProjectImpact;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectImpact\ImportProjectImpactRequest;
use App\Http\Requests\ProjectImpact\StoreProjectImpactRequest;
use App\Http\Requests\ProjectImpact\UpdateProjectImpactRequest;
use App\Imports\ProjectImpactImporter;
use App\Models\ProjectImpact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ProjectImpactController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', ProjectImpact::class);

        $query = ProjectImpact::query()
            ->when($request->string('search')->trim()->isNotEmpty(), function ($query) use ($request): void {
                $search = $request->string('search')->trim()->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('project_title', 'like', "%{$search}%")
                        ->orWhere('proponent', 'like', "%{$search}%")
                        ->orWhere('program_intervention', 'like', "%{$search}%")
                        ->orWhere('impact_narrative', 'like', "%{$search}%");
                });
            })
            ->when(
                $request->filled('source_sheet'),
                fn ($query) => $query->where('source_sheet', $request->string('source_sheet')->toString()),
            )
            ->when(
                $request->filled('classification'),
                fn ($query) => $query->where('classification', $request->string('classification')->toString()),
            )
            ->when(
                $request->filled('project_status'),
                fn ($query) => $query->where('project_status', $request->string('project_status')->toString()),
            );

        $perPage = in_array($request->integer('per_page'), [10, 15, 25, 50, 100], true)
            ? $request->integer('per_page')
            : 15;

        return Inertia::render('admin/project-impact-tracking/Index', [
            'records' => $query->latest()->paginate($perPage)->withQueryString(),
            'filters' => $request->only([
                'search',
                'source_sheet',
                'classification',
                'project_status',
                'per_page',
            ]),
            'options' => [
                'sourceSheets' => ProjectImpact::query()->distinct()->orderBy('source_sheet')->pluck('source_sheet'),
                'classifications' => ProjectImpact::query()->whereNotNull('classification')->distinct()->orderBy('classification')->pluck('classification'),
                'statuses' => ProjectImpact::query()->whereNotNull('project_status')->distinct()->orderBy('project_status')->pluck('project_status'),
            ],
        ]);
    }

    public function store(StoreProjectImpactRequest $request): RedirectResponse
    {
        ProjectImpact::query()->create($request->validated());

        return to_route('admin.project-impact-tracking.index')
            ->with('success', 'Project impact record created successfully.');
    }

    public function update(
        UpdateProjectImpactRequest $request,
        ProjectImpact $projectImpact,
    ): RedirectResponse {
        $projectImpact->update($request->validated());

        return to_route('admin.project-impact-tracking.index')
            ->with('success', 'Project impact record updated successfully.');
    }

    public function destroy(ProjectImpact $projectImpact): RedirectResponse
    {
        Gate::authorize('delete', $projectImpact);
        $projectImpact->delete();

        return to_route('admin.project-impact-tracking.index')
            ->with('success', 'Project impact record deleted successfully.');
    }

    public function import(
        ImportProjectImpactRequest $request,
        ProjectImpactImporter $importer,
    ): RedirectResponse {
        $file = $request->file('file');
        assert($file instanceof UploadedFile);
        $count = $importer->import($file);

        return to_route('admin.project-impact-tracking.index')
            ->with('success', "{$count} project impact records imported successfully.");
    }
}

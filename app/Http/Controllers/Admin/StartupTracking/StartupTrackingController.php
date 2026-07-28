<?php

namespace App\Http\Controllers\Admin\StartupTracking;

use App\Http\Controllers\Controller;
use App\Http\Requests\StartupTracking\ImportStartupTrackingRequest;
use App\Http\Requests\StartupTracking\StoreStartupTrackingRequest;
use App\Http\Requests\StartupTracking\UpdateStartupTrackingRequest;
use App\Imports\StartupTrackingImporter;
use App\Models\StartupTracking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class StartupTrackingController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', StartupTracking::class);

        $query = StartupTracking::query()
            ->when($request->string('search')->trim()->isNotEmpty(), function ($query) use ($request): void {
                $search = $request->string('search')->trim()->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('project_title', 'like', "%{$search}%")
                        ->orWhere('proponent_name', 'like', "%{$search}%")
                        ->orWhere('contact_details', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->when($request->filled('program'), fn ($query) => $query->where('program', $request->string('program')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')));

        $perPage = in_array($request->integer('per_page'), [10, 15, 25, 50, 100], true)
            ? $request->integer('per_page')
            : 15;

        return Inertia::render('admin/startup-tracking/Index', [
            'records' => $query->latest()->paginate($perPage)->withQueryString(),
            'filters' => $request->only(['search', 'type', 'program', 'status', 'per_page']),
            'options' => [
                'types' => StartupTracking::query()->whereNotNull('type')->distinct()->orderBy('type')->pluck('type'),
                'programs' => StartupTracking::query()->whereNotNull('program')->distinct()->orderBy('program')->pluck('program'),
                'statuses' => StartupTracking::query()->whereNotNull('status')->distinct()->orderBy('status')->pluck('status'),
            ],
        ]);
    }

    public function store(StoreStartupTrackingRequest $request): RedirectResponse
    {
        StartupTracking::query()->create($request->validated());

        return to_route('admin.startup-tracking.index')
            ->with('success', 'Startup tracking record created successfully.');
    }

    public function update(
        UpdateStartupTrackingRequest $request,
        StartupTracking $startupTracking,
    ): RedirectResponse {
        $startupTracking->update($request->validated());

        return to_route('admin.startup-tracking.index')
            ->with('success', 'Startup tracking record updated successfully.');
    }

    public function destroy(StartupTracking $startupTracking): RedirectResponse
    {
        Gate::authorize('delete', $startupTracking);
        $startupTracking->delete();

        return to_route('admin.startup-tracking.index')
            ->with('success', 'Startup tracking record deleted successfully.');
    }

    public function import(
        ImportStartupTrackingRequest $request,
        StartupTrackingImporter $importer,
    ): RedirectResponse {
        $file = $request->file('file');
        assert($file instanceof UploadedFile);
        $count = $importer->import($file);

        return to_route('admin.startup-tracking.index')
            ->with('success', "{$count} startup tracking records imported successfully.");
    }
}

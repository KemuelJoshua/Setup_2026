<?php

namespace App\Http\Controllers\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreSectionRequest;
use App\Http\Requests\Academics\UpdateSectionRequest;
use App\Models\Academics\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SectionController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('admin view sections');

        $perPage = $request->integer('per_page', 15);

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        $sections = Section::query()
            ->when($filters['search'], function (Builder $query) use ($filters) {
                $query->where('name', 'like', "%{$filters['search']}%");
            })
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn (Section $section): array => [
                'id' => $section->getKey(),
                'name' => $section->name,
                'created_at' => $section->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('admin/academics/sections/Index', [
            'sections' => $sections,
            'filters' => $filters,
        ]);
    }

    public function store(StoreSectionRequest $request): RedirectResponse
    {
        Gate::authorize('admin create sections');

        Section::query()->create($request->validated());

        return redirect()
            ->route('admin.academics.section.index')
            ->with('success', 'Section created successfully.');
    }

    public function update(
        UpdateSectionRequest $request,
        Section $section,
    ): RedirectResponse {
        Gate::authorize('admin update sections');

        $section->update($request->validated());

        return redirect()
            ->route('admin.academics.section.index')
            ->with('success', 'Section updated successfully.');
    }

    public function destroy(Section $section): RedirectResponse
    {
        Gate::authorize('admin delete sections');

        $section->delete();

        return redirect()
            ->route('admin.academics.section.index')
            ->with('success', 'Section deleted successfully.');
    }
}

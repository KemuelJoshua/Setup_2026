<?php

namespace App\Http\Controllers\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreSubjectRequest;
use App\Http\Requests\Academics\UpdateSubjectRequest;
use App\Models\Academics\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SubjectController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('admin view subjects');

        $perPage = $request->integer('per_page', 15);

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        $subjects = Subject::query()
            ->when($filters['search'], function (Builder $query) use ($filters) {
                $query->where('name', 'like', "%{$filters['search']}%");
            })
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn (Subject $subject): array => [
                'id' => $subject->getKey(),
                'name' => $subject->name,
                'created_at' => $subject->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('admin/academics/subjects/Index', [
            'subjects' => $subjects,
            'filters' => $filters,
        ]);
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Gate::authorize('admin create subjects');

        Subject::query()->create($request->validated());

        return redirect()
            ->route('admin.academics.subject.index')
            ->with('success', 'Subject created successfully.');
    }

    public function update(
        UpdateSubjectRequest $request,
        Subject $subject,
    ): RedirectResponse {
        Gate::authorize('admin update subjects');

        $subject->update($request->validated());

        return redirect()
            ->route('admin.academics.subject.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        Gate::authorize('admin delete subjects');

        $subject->delete();

        return redirect()
            ->route('admin.academics.subject.index')
            ->with('success', 'Subject deleted successfully.');
    }
}

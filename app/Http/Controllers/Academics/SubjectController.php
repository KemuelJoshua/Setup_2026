<?php

namespace App\Http\Controllers\Academics;

use App\Actions\Academics\Subject\CreateSubjectAction;
use App\Actions\Academics\Subject\DeleteSubjectAction;
use App\Actions\Academics\Subject\IndexSubjectAction;
use App\Actions\Academics\Subject\UpdateSubjectAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreSubjectRequest;
use App\Http\Requests\Academics\UpdateSubjectRequest;
use App\Models\Academics\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SubjectController extends Controller
{
    public function index(Request $request, IndexSubjectAction $indexSubjects): Response
    {
        Gate::authorize('admin view subjects');

        $perPage = $request->integer('per_page', 15);

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        $subjects = $indexSubjects
            ->execute($filters)
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

    public function store(
        StoreSubjectRequest $request,
        CreateSubjectAction $createSubject,
    ): RedirectResponse {
        Gate::authorize('admin create subjects');

        $createSubject->execute($request->validated());

        return redirect()
            ->route('admin.academics.subject.index')
            ->with('success', 'Subject created successfully.');
    }

    public function update(
        UpdateSubjectRequest $request,
        Subject $subject,
        UpdateSubjectAction $updateSubject,
    ): RedirectResponse {
        Gate::authorize('admin update subjects');

        $updateSubject->execute($subject, $request->validated());

        return redirect()
            ->route('admin.academics.subject.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(
        Subject $subject,
        DeleteSubjectAction $deleteSubject,
    ): RedirectResponse {
        Gate::authorize('admin delete subjects');

        $deleteSubject->execute($subject);

        return redirect()
            ->route('admin.academics.subject.index')
            ->with('success', 'Subject deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Academics;

use App\Actions\Academics\Curriculum\CreateCurriculumSubjectAction;
use App\Actions\Academics\Curriculum\UpdateCurriculumSubjectAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreCurriculumSubjectRequest;
use App\Http\Requests\Academics\UpdateCurriculumSubjectRequest;
use App\Models\Academics\Curriculum;
use App\Models\Academics\CurriculumSubject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class CurriculumSubjectController extends Controller
{
    public function store(
        StoreCurriculumSubjectRequest $request,
        Curriculum $curriculum,
        CreateCurriculumSubjectAction $createCurriculumSubject,
    ): RedirectResponse {
        Gate::authorize('admin update curricula');

        $createCurriculumSubject->execute($curriculum, $request->validated());

        return back()->with('success', 'Subject added successfully.');
    }

    public function update(
        UpdateCurriculumSubjectRequest $request,
        Curriculum $curriculum,
        CurriculumSubject $curriculumSubject,
        UpdateCurriculumSubjectAction $updateCurriculumSubject,
    ): RedirectResponse {
        Gate::authorize('admin update curricula');
        abort_unless($curriculumSubject->curriculum_id === $curriculum->getKey(), 404);

        $updateCurriculumSubject->execute($curriculumSubject, $request->validated());

        return back()->with('success', 'Subject updated successfully.');
    }

    public function destroy(
        Curriculum $curriculum,
        CurriculumSubject $curriculumSubject,
    ): RedirectResponse {
        Gate::authorize('admin update curricula');
        abort_unless($curriculumSubject->curriculum_id === $curriculum->getKey(), 404);

        $curriculumSubject->delete();

        return back()->with('success', 'Subject removed successfully.');
    }
}

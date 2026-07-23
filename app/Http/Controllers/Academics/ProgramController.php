<?php

namespace App\Http\Controllers\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreProgramRequest;
use App\Http\Requests\Academics\UpdateProgramRequest;
use App\Models\Academics\Program;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ProgramController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('admin view programs');

        $perPage = $request->integer('per_page', 15);

        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        $programs = Program::query()
            ->when($filters['search'], function (Builder $query) use ($filters) {
                $search = $filters['search'];

                $query->where(function (Builder $query) use ($search) {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn (Program $program): array => [
                'id' => $program->getKey(),
                'code' => $program->code,
                'name' => $program->name,
                'description' => $program->description,
                'status' => $program->status,
                'created_at' => $program->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('admin/academics/programs/Index', [
            'programs' => $programs,
            'filters' => $filters,
        ]);
    }

    public function store(StoreProgramRequest $request): RedirectResponse
    {
        Gate::authorize('admin create programs');

        Program::query()->create($request->validated());

        return redirect()
            ->route('admin.academics.program.index')
            ->with('success', 'Program created successfully.');
    }

    public function update(
        UpdateProgramRequest $request,
        Program $program,
    ): RedirectResponse {
        Gate::authorize('admin update programs');

        $program->update($request->validated());

        return redirect()
            ->route('admin.academics.program.index')
            ->with('success', 'Program updated successfully.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        Gate::authorize('admin delete programs');

        $program->delete();

        return redirect()
            ->route('admin.academics.program.index')
            ->with('success', 'Program deleted successfully.');
    }
}

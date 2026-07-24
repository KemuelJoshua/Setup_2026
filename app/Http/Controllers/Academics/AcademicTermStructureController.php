<?php

namespace App\Http\Controllers\Academics;

use App\Actions\Academics\AcademicTermStructure\CreateAcademicTermStructureAction;
use App\Actions\Academics\AcademicTermStructure\DeleteAcademicTermStructureAction;
use App\Actions\Academics\AcademicTermStructure\IndexAcademicTermStructureAction;
use App\Actions\Academics\AcademicTermStructure\UpdateAcademicTermStructureAction;
use App\Actions\Academics\EducationalLevel\ListEducationalLevelOptionsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreAcademicTermStructureRequest;
use App\Http\Requests\Academics\UpdateAcademicTermStructureRequest;
use App\Models\Academics\AcademicTermStructure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class AcademicTermStructureController extends Controller
{
    public function index(
        Request $request,
        IndexAcademicTermStructureAction $indexAcademicTermStructures,
        ListEducationalLevelOptionsAction $listEducationalLevelOptions,
    ): Response {
        Gate::authorize('admin view academic term structures');

        $perPage = $request->integer('per_page', 15);
        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'educational_level_id' => $request->integer('educational_level_id') ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        return Inertia::render('admin/academics/academic-term-structures/Index', [
            'academicTermStructures' => $indexAcademicTermStructures
                ->execute($filters)
                ->paginate($filters['per_page'])
                ->withQueryString(),
            'filters' => $filters,
            'educationalLevels' => $listEducationalLevelOptions->execute(),
        ]);
    }

    public function store(
        StoreAcademicTermStructureRequest $request,
        CreateAcademicTermStructureAction $createAcademicTermStructure,
    ): RedirectResponse {
        Gate::authorize('admin create academic term structures');
        /** @var array{educational_level_id: int, name: string, code: string, type: string, status: string} $data */
        $data = $request->validated();
        $createAcademicTermStructure->execute($data);

        return to_route('admin.academics.academic-term-structures.index')
            ->with('success', 'Academic term structure created successfully.');
    }

    public function update(
        UpdateAcademicTermStructureRequest $request,
        AcademicTermStructure $academicTermStructure,
        UpdateAcademicTermStructureAction $updateAcademicTermStructure,
    ): RedirectResponse {
        Gate::authorize('admin update academic term structures');
        /** @var array{educational_level_id: int, name: string, code: string, type: string, status: string} $data */
        $data = $request->validated();
        $updateAcademicTermStructure->execute($academicTermStructure, $data);

        return to_route('admin.academics.academic-term-structures.index')
            ->with('success', 'Academic term structure updated successfully.');
    }

    public function destroy(
        AcademicTermStructure $academicTermStructure,
        DeleteAcademicTermStructureAction $deleteAcademicTermStructure,
    ): RedirectResponse {
        Gate::authorize('admin delete academic term structures');
        $deleteAcademicTermStructure->execute($academicTermStructure);

        return to_route('admin.academics.academic-term-structures.index')
            ->with('success', 'Academic term structure deleted successfully.');
    }
}

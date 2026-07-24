<?php

namespace App\Http\Controllers\Academics;

use App\Actions\Academics\AcademicPeriod\CreateAcademicPeriodAction;
use App\Actions\Academics\AcademicPeriod\DeleteAcademicPeriodAction;
use App\Actions\Academics\AcademicPeriod\UpdateAcademicPeriodAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreAcademicPeriodRequest;
use App\Http\Requests\Academics\UpdateAcademicPeriodRequest;
use App\Models\Academics\AcademicPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class AcademicPeriodController extends Controller
{
    public function store(
        StoreAcademicPeriodRequest $request,
        CreateAcademicPeriodAction $createAcademicPeriod,
    ): RedirectResponse {
        Gate::authorize('admin create academic periods');
        /** @var array{academic_term_structure_id: int, parent_id?: int|null, name: string, code?: string|null, sequence: int, status: string} $data */
        $data = $request->validated();
        $createAcademicPeriod->execute($data);

        return to_route('admin.academics.academic-term-structures.index')
            ->with('success', 'Academic period created successfully.');
    }

    public function update(
        UpdateAcademicPeriodRequest $request,
        AcademicPeriod $academicPeriod,
        UpdateAcademicPeriodAction $updateAcademicPeriod,
    ): RedirectResponse {
        Gate::authorize('admin update academic periods');
        /** @var array{academic_term_structure_id: int, parent_id?: int|null, name: string, code?: string|null, sequence: int, status: string} $data */
        $data = $request->validated();
        $updateAcademicPeriod->execute($academicPeriod, $data);

        return to_route('admin.academics.academic-term-structures.index')
            ->with('success', 'Academic period updated successfully.');
    }

    public function destroy(
        AcademicPeriod $academicPeriod,
        DeleteAcademicPeriodAction $deleteAcademicPeriod,
    ): RedirectResponse {
        Gate::authorize('admin delete academic periods');
        $deleteAcademicPeriod->execute($academicPeriod);

        return to_route('admin.academics.academic-term-structures.index')
            ->with('success', 'Academic period deleted successfully.');
    }
}

<?php

namespace App\Actions\Academics\AcademicTermStructure;

use App\Enums\AcademicTermStructureType;
use App\Models\Academics\AcademicTermStructure;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateAcademicTermStructureAction
{
    /**
     * @param  array{name: string, code: string, type: string, status: string}  $data
     */
    public function execute(
        AcademicTermStructure $academicTermStructure,
        array $data,
    ): AcademicTermStructure {
        return DB::transaction(function () use ($academicTermStructure, $data): AcademicTermStructure {
            /** @var AcademicTermStructure $lockedStructure */
            $lockedStructure = AcademicTermStructure::query()
                ->lockForUpdate()
                ->findOrFail($academicTermStructure->getKey());

            $type = AcademicTermStructureType::from($data['type']);

            if (
                ! $type->allowsChildPeriods()
                && $lockedStructure->periods()->whereNotNull('parent_id')->exists()
            ) {
                throw ValidationException::withMessages([
                    'type' => 'Quarterly structures cannot contain grading periods.',
                ]);
            }

            $lockedStructure->update($data);

            return $lockedStructure->refresh();
        });
    }
}

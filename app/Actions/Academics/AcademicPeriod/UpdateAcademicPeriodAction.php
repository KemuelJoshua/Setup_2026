<?php

namespace App\Actions\Academics\AcademicPeriod;

use App\Models\Academics\AcademicPeriod;
use Illuminate\Support\Facades\DB;

class UpdateAcademicPeriodAction
{
    public function __construct(
        private AcademicPeriodValidator $validator,
    ) {}

    /**
     * @param  array{
     *     academic_term_structure_id: int,
     *     parent_id?: int|null,
     *     name: string,
     *     code?: string|null,
     *     sequence: int,
     *     status: string
     * }  $data
     */
    public function execute(AcademicPeriod $academicPeriod, array $data): AcademicPeriod
    {
        return DB::transaction(function () use ($academicPeriod, $data): AcademicPeriod {
            /** @var AcademicPeriod $lockedAcademicPeriod */
            $lockedAcademicPeriod = AcademicPeriod::query()
                ->lockForUpdate()
                ->findOrFail($academicPeriod->getKey());

            $this->validator->validate($data, $lockedAcademicPeriod);
            $lockedAcademicPeriod->update($data);

            return $lockedAcademicPeriod->refresh();
        });
    }
}

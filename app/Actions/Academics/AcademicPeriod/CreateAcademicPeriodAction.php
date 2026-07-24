<?php

namespace App\Actions\Academics\AcademicPeriod;

use App\Concerns\ValidatesAcademicPeriods;
use App\Models\Academics\AcademicPeriod;
use Illuminate\Support\Facades\DB;

class CreateAcademicPeriodAction
{
    use ValidatesAcademicPeriods;

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
    public function execute(array $data): AcademicPeriod
    {
        return DB::transaction(function () use ($data): AcademicPeriod {
            $this->validateAcademicPeriod($data);

            return AcademicPeriod::query()->create($data);
        });
    }
}

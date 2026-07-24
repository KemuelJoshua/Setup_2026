<?php

namespace App\Actions\Academics\AcademicTermStructure;

use App\Models\Academics\AcademicTermStructure;
use Illuminate\Support\Facades\DB;

class CreateAcademicTermStructureAction
{
    /**
     * @param  array{name: string, code: string, type: string, status: string}  $data
     */
    public function execute(array $data): AcademicTermStructure
    {
        return DB::transaction(
            fn (): AcademicTermStructure => AcademicTermStructure::query()->create($data),
        );
    }
}

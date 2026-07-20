<?php

namespace App\Actions\Academics\SchoolYear;

use App\Enums\SchoolYearStatus;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\DB;

class ChangeStatusSchoolYearAction
{
    public function execute(string $id, array $data): SchoolYear
    {
        return DB::transaction(function () use ($id, $data) {
            $schoolYear = SchoolYear::query()
                ->lockForUpdate()
                ->findOrFail($id);

            $status = SchoolYearStatus::from($data['sc_status']);

            switch ($status) {
                case SchoolYearStatus::ACTIVE:
                    SchoolYear::query()
                        ->whereKeyNot($schoolYear->id)
                        ->where('sc_status', SchoolYearStatus::ACTIVE)
                        ->update([
                            'sc_status' => SchoolYearStatus::CLOSED,
                        ]);
                    break;

                case SchoolYearStatus::CLOSED:
                    // Nothing extra to do.
                    break;

                case SchoolYearStatus::PLANNED:
                    // Nothing extra to do.
                    break;
            }

            $schoolYear->update([
                'sc_name' => $data['sc_name'],
                'sc_code' => $data['sc_code'],
                'sc_start_date' => $data['sc_start_date'],
                'sc_end_date' => $data['sc_end_date'],
                'sc_status' => $status,
            ]);

            return $schoolYear->refresh();
        });
    }
}

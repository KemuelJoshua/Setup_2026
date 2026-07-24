<?php

namespace App\Concerns;

use App\Models\Academics\AcademicPeriod;
use App\Models\Academics\AcademicTermStructure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait ValidatesAcademicPeriods
{
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
    protected function validateAcademicPeriod(
        array $data,
        ?AcademicPeriod $current = null,
    ): void {
        $parentId = $data['parent_id'] ?? null;

        if ($current && $parentId === $current->getKey()) {
            $this->failAcademicPeriodValidation(
                'parent_id',
                'An academic period cannot be its own parent.',
            );
        }

        if ($parentId !== null) {
            /** @var AcademicTermStructure $structure */
            $structure = AcademicTermStructure::query()
                ->lockForUpdate()
                ->findOrFail($data['academic_term_structure_id']);

            /** @var AcademicPeriod|null $parent */
            $parent = AcademicPeriod::query()->lockForUpdate()->find($parentId);

            if (! $parent) {
                $this->failAcademicPeriodValidation(
                    'parent_id',
                    'The selected parent academic period is invalid.',
                );
            }

            if ($parent->academic_term_structure_id !== $data['academic_term_structure_id']) {
                $this->failAcademicPeriodValidation(
                    'parent_id',
                    'The parent must belong to the same academic term structure.',
                );
            }

            if ($parent->parent_id !== null) {
                $this->failAcademicPeriodValidation(
                    'parent_id',
                    'Only a root academic period may be selected as a parent.',
                );
            }

            if ($current && $current->children()->exists()) {
                $this->failAcademicPeriodValidation(
                    'parent_id',
                    'A period with children cannot become a child period.',
                );
            }

            if (! $structure->type->allowsChildPeriods()) {
                $this->failAcademicPeriodValidation(
                    'parent_id',
                    'Quarterly structures do not allow grading periods.',
                );
            }

            $childCount = AcademicPeriod::query()
                ->where('parent_id', $parentId)
                ->when(
                    $current,
                    fn (Builder $query): Builder => $query->whereKeyNot($current->getKey()),
                )
                ->count();

            if ($childCount >= $structure->type->maximumChildPeriods()) {
                $this->failAcademicPeriodValidation(
                    'parent_id',
                    'A root period may contain at most four grading periods.',
                );
            }
        }

        $siblings = AcademicPeriod::query()
            ->where('academic_term_structure_id', $data['academic_term_structure_id'])
            ->when(
                $parentId === null,
                fn (Builder $query): Builder => $query->whereNull('parent_id'),
                fn (Builder $query): Builder => $query->where('parent_id', $parentId),
            )
            ->when(
                $current,
                fn (Builder $query): Builder => $query->whereKeyNot($current->getKey()),
            )
            ->lockForUpdate();

        if ((clone $siblings)->whereRaw('LOWER(name) = ?', [Str::lower($data['name'])])->exists()) {
            $this->failAcademicPeriodValidation(
                'name',
                'A period with this name already exists at the same level.',
            );
        }

        if ((clone $siblings)->where('sequence', $data['sequence'])->exists()) {
            $this->failAcademicPeriodValidation(
                'sequence',
                'A period with this sequence already exists at the same level.',
            );
        }
    }

    private function failAcademicPeriodValidation(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}

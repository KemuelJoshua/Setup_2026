<?php

namespace App\Http\Requests\Academics;

use App\Enums\AcademicStatus;
use App\Models\Academics\AcademicPeriod;
use App\Models\Academics\AcademicTermStructure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

abstract class AcademicPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $current = $this->academicPeriod();
        $structureId = $this->integer('academic_term_structure_id');
        $parentId = $this->input('parent_id');

        $scope = fn ($query) => $query
            ->where('academic_term_structure_id', $structureId)
            ->when(
                blank($parentId),
                fn ($query) => $query->whereNull('parent_id'),
                fn ($query) => $query->where('parent_id', $parentId),
            );

        return [
            'academic_term_structure_id' => [
                'required',
                'integer',
                'exists:academic_term_structures,id',
            ],
            'parent_id' => ['nullable', 'integer', 'exists:academic_periods,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(AcademicPeriod::class, 'name')
                    ->where($scope)
                    ->ignore($current),
            ],
            'code' => ['nullable', 'string', 'max:255'],
            'sequence' => [
                'required',
                'integer',
                'min:1',
                Rule::unique(AcademicPeriod::class, 'sequence')
                    ->where($scope)
                    ->ignore($current),
            ],
            'status' => ['required', Rule::enum(AcademicStatus::class)],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $current = $this->academicPeriod();
                $parentId = $this->input('parent_id');

                if ($current && (int) $parentId === $current->getKey()) {
                    $validator->errors()->add('parent_id', 'An academic period cannot be its own parent.');

                    return;
                }

                if (blank($parentId)) {
                    return;
                }

                /** @var AcademicPeriod|null $parent */
                $parent = AcademicPeriod::query()->find($parentId);

                if (! $parent) {
                    return;
                }

                if ($parent->academic_term_structure_id !== $this->integer('academic_term_structure_id')) {
                    $validator->errors()->add('parent_id', 'The parent must belong to the same academic term structure.');
                }

                if ($parent->parent_id !== null) {
                    $validator->errors()->add('parent_id', 'Only a root academic period may be selected as a parent.');
                }

                if ($current?->children()->exists()) {
                    $validator->errors()->add('parent_id', 'A period with children cannot become a child period.');
                }

                /** @var AcademicTermStructure|null $structure */
                $structure = AcademicTermStructure::query()
                    ->find($this->integer('academic_term_structure_id'));

                if (! $structure) {
                    return;
                }

                if (! $structure->type->allowsChildPeriods()) {
                    $validator->errors()->add(
                        'parent_id',
                        'Quarterly structures do not allow grading periods.',
                    );

                    return;
                }

                $childCount = AcademicPeriod::query()
                    ->where('parent_id', $parent->getKey())
                    ->when(
                        $current,
                        fn ($query) => $query->whereKeyNot($current->getKey()),
                    )
                    ->count();

                if ($childCount >= $structure->type->maximumChildPeriods()) {
                    $validator->errors()->add(
                        'parent_id',
                        'A root period may contain at most four grading periods.',
                    );
                }
            },
        ];
    }

    protected function academicPeriod(): ?AcademicPeriod
    {
        $academicPeriod = $this->route('academic_period');

        return $academicPeriod instanceof AcademicPeriod ? $academicPeriod : null;
    }
}

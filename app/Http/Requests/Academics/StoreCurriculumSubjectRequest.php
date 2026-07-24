<?php

namespace App\Http\Requests\Academics;

use App\Models\Academics\AcademicPeriod;
use App\Models\Academics\Curriculum;
use App\Models\Academics\CurriculumSubject;
use App\Models\Academics\GradeLevel;
use App\Models\Academics\Subject;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCurriculumSubjectRequest extends FormRequest
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
        $curriculum = $this->curriculum();
        $curriculumSubject = $this->route('curriculumSubject');

        return [
            'subject_id' => [
                'required',
                'integer',
                Rule::exists(Subject::class, 'id'),
                Rule::unique(CurriculumSubject::class, 'subject_id')
                    ->where(fn ($query) => $query
                        ->where('curriculum_id', $curriculum->getKey())
                        ->where('year_level_id', $this->input('year_level_id'))
                        ->where('academic_period_id', $this->input('academic_period_id')))
                    ->ignore($curriculumSubject),
            ],
            'year_level_id' => [
                'required',
                'integer',
                Rule::exists(GradeLevel::class, 'id')
                    ->where(fn ($query) => $query->whereIn(
                        'name',
                        $this->allowedYearLevelNames($curriculum),
                    )),
            ],
            'academic_period_id' => [
                'required',
                'integer',
                Rule::exists(AcademicPeriod::class, 'id')
                    ->where('academic_term_structure_id', $curriculum->academic_term_structure_id)
                    ->whereNull('parent_id'),
            ],
            'units' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'lecture_hours' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'laboratory_hours' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'prerequisite_ids' => ['nullable', 'array'],
            'prerequisite_ids.*' => [
                'integer',
                'distinct',
                Rule::exists(CurriculumSubject::class, 'id')
                    ->where('curriculum_id', $curriculum->getKey()),
            ],
            'corequisite_ids' => ['nullable', 'array'],
            'corequisite_ids.*' => [
                'integer',
                'distinct',
                Rule::exists(CurriculumSubject::class, 'id')
                    ->where('curriculum_id', $curriculum->getKey()),
            ],
        ];
    }

    /**
     * @return array<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $curriculumSubject = $this->route('curriculumSubject');

                if (! $curriculumSubject instanceof CurriculumSubject) {
                    return;
                }

                foreach (['prerequisite_ids', 'corequisite_ids'] as $field) {
                    $referenceIds = array_map(
                        static fn (mixed $id): int => (int) $id,
                        $this->array($field),
                    );

                    if (in_array($curriculumSubject->getKey(), $referenceIds, true)) {
                        $validator->errors()->add($field, 'A subject cannot reference itself.');
                    }
                }
            },
        ];
    }

    private function curriculum(): Curriculum
    {
        $curriculum = $this->route('curriculum');

        abort_unless($curriculum instanceof Curriculum, 404);

        return $curriculum;
    }

    /**
     * @return list<string>
     */
    private function allowedYearLevelNames(Curriculum $curriculum): array
    {
        $structureCode = $curriculum->academicTermStructure()->value('code');

        return match (true) {
            str_starts_with((string) $structureCode, 'C') => array_map(
                static fn (int $year): string => "Year {$year}",
                range(1, $curriculum->number_of_years),
            ),
            $structureCode === 'JHS4Q' => array_slice(
                ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'],
                0,
                $curriculum->number_of_years,
            ),
            $structureCode === 'SHS4Q' => array_slice(
                ['Grade 11', 'Grade 12'],
                0,
                $curriculum->number_of_years,
            ),
            default => GradeLevel::query()->orderBy('id')->pluck('name')->all(),
        };
    }
}

<?php

namespace App\Http\Requests\Academics;

use App\Models\Academics\Curriculum;
use App\Models\Academics\Program;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCurriculumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $curriculum = $this->route('curriculum');
        $structureCode = $curriculum instanceof Curriculum
            ? $curriculum->academicTermStructure()->value('code')
            : null;
        $educationalLevelId = $curriculum instanceof Curriculum
            ? $curriculum->academicTermStructure()->value('educational_level_id')
            : null;

        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Curriculum::class, 'code')->ignore($curriculum),
            ],
            'name' => ['required', 'string', 'max:255'],
            'program_id' => [
                'required',
                'integer',
                Rule::exists(Program::class, 'id')
                    ->where('educational_level_id', $educationalLevelId),
            ],
            'academic_term_structure_id' => ['prohibited'],
            'effective_year' => ['required', 'integer', 'min:1900', 'max:9999'],
            'number_of_years' => [
                'required',
                'integer',
                'min:1',
                'max:'.$this->maximumYearsFor($structureCode),
            ],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['Draft', 'Active', 'Inactive'])],
        ];
    }

    private function maximumYearsFor(?string $structureCode): int
    {
        return match ($structureCode) {
            'JHS4Q' => 4,
            'SHS4Q' => 2,
            default => 10,
        };
    }
}

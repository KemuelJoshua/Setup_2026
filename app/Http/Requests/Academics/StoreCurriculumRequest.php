<?php

namespace App\Http\Requests\Academics;

use App\Models\Academics\AcademicTermStructure;
use App\Models\Academics\Curriculum;
use App\Models\Academics\Program;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCurriculumRequest extends FormRequest
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
        $programEducationalLevelId = Program::query()
            ->whereKey($this->integer('program_id'))
            ->value('educational_level_id');
        $structureCode = AcademicTermStructure::query()
            ->whereKey($this->integer('academic_term_structure_id'))
            ->value('code');

        return [
            'code' => ['required', 'string', 'max:255', Rule::unique(Curriculum::class, 'code')],
            'name' => ['required', 'string', 'max:255'],
            'program_id' => ['required', 'integer', Rule::exists(Program::class, 'id')],
            'academic_term_structure_id' => [
                'required',
                'integer',
                Rule::exists(AcademicTermStructure::class, 'id')
                    ->where('status', 'active')
                    ->where('educational_level_id', $programEducationalLevelId),
            ],
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

    protected function maximumYearsFor(?string $structureCode): int
    {
        return match ($structureCode) {
            'JHS4Q' => 4,
            'SHS4Q' => 2,
            default => 10,
        };
    }
}

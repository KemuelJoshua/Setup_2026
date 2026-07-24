<?php

namespace App\Http\Requests\Academics;

use App\Models\Academics\Curriculum;
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

        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Curriculum::class, 'code')->ignore($curriculum),
            ],
            'name' => ['required', 'string', 'max:255'],
            'effective_year' => ['required', 'integer', 'min:1900', 'max:9999'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:255'],
            'curriculum_subjects' => ['nullable', 'array'],
            'curriculum_subjects.*.subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'curriculum_subjects.*.year_level_id' => ['required', 'integer', 'exists:grade_levels,id'],
            'curriculum_subjects.*.academic_period_id' => ['required', 'integer', 'exists:academic_periods,id'],
            'curriculum_subjects.*.is_required' => ['required', 'boolean'],
            'curriculum_subjects.*.sort_order' => ['required', 'integer', 'min:0'],
        ];
    }
}

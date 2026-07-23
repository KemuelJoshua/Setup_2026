<?php

namespace App\Http\Requests\Academics;

use App\Models\Academics\AcademicTerm;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicTermRequest extends FormRequest
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
        $academicTerm = $this->route('academicTerm');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique(AcademicTerm::class, 'code')->ignore($academicTerm),
            ],
            'type' => ['required', Rule::in(['Quarter', 'Semester', 'Not Applicable'])],
            'grading_periods' => ['nullable', 'array'],
            'grading_periods.*.name' => ['required', 'string', 'max:255'],
            'grading_periods.*.code' => ['required', 'string', 'max:255', 'distinct:ignore_case'],
            'grading_periods.*.sort_order' => ['required', 'integer', 'min:0'],
        ];
    }
}

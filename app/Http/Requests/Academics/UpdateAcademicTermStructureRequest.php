<?php

namespace App\Http\Requests\Academics;

use App\Enums\AcademicStatus;
use App\Enums\AcademicTermStructureType;
use App\Models\Academics\AcademicTermStructure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateAcademicTermStructureRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique(AcademicTermStructure::class, 'code')
                    ->ignore($this->route('academic_term_structure')),
            ],
            'type' => ['required', Rule::enum(AcademicTermStructureType::class)],
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

                $structure = $this->route('academic_term_structure');
                $type = AcademicTermStructureType::tryFrom($this->string('type')->toString());

                if (
                    $structure instanceof AcademicTermStructure
                    && $type !== null
                    && ! $type->allowsChildPeriods()
                    && $structure->periods()->whereNotNull('parent_id')->exists()
                ) {
                    $validator->errors()->add(
                        'type',
                        'Quarterly structures cannot contain grading periods.',
                    );
                }
            },
        ];
    }
}

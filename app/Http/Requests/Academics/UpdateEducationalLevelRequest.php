<?php

namespace App\Http\Requests\Academics;

use App\Models\Academics\EducationalLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEducationalLevelRequest extends FormRequest
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
        $educationalLevel = $this->route('educationalLevel');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(EducationalLevel::class, 'name')->ignore($educationalLevel),
            ],
        ];
    }
}

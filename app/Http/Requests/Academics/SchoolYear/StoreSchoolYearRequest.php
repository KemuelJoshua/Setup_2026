<?php

namespace App\Http\Requests\Academics\SchoolYear;

use App\Enums\SchoolYearStatus;
use App\Models\SchoolYear;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSchoolYearRequest extends FormRequest
{
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
            'sc_name' => ['required', 'string', 'max:255'],
            'sc_code' => ['required', 'string', 'max:255', Rule::unique(SchoolYear::class, 'sc_code')],
            'sc_start_date' => ['required', 'date'],
            'sc_end_date' => ['required', 'date', 'after:sc_start_date'],
            'sc_status' => ['required', Rule::enum(SchoolYearStatus::class)],
        ];
    }
}

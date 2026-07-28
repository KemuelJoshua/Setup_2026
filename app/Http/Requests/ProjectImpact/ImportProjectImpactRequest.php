<?php

namespace App\Http\Requests\ProjectImpact;

use App\Models\ProjectImpact;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportProjectImpactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('import', ProjectImpact::class) === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:20480',
                'extensions:csv,xlsx',
                'mimetypes:text/csv,text/plain,application/zip,application/octet-stream,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
        ];
    }
}

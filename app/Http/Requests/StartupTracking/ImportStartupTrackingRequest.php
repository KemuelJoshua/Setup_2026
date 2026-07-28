<?php

namespace App\Http\Requests\StartupTracking;

use App\Models\StartupTracking;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportStartupTrackingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('import', StartupTracking::class) === true;
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
                'max:10240',
                'extensions:csv,xlsx',
                'mimetypes:text/csv,text/plain,application/zip,application/octet-stream,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
        ];
    }
}

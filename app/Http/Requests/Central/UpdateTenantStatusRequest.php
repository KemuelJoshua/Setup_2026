<?php

namespace App\Http\Requests\Central;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ! tenancy()->initialized;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'is_active' => ['required', 'boolean'],
        ];
    }
}

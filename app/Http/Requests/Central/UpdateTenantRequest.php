<?php

namespace App\Http\Requests\Central;

use App\Models\Tenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Stancl\Tenancy\Database\Models\Domain;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ! tenancy()->initialized;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var Tenant $tenant */
        $tenant = $this->route('tenant');
        $centralDomains = array_map('mb_strtolower', config('tenancy.central_domains', []));

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'school_code' => ['required', 'string', 'max:255', Rule::unique('tenants', 'school_code')->ignore($tenant)],
            'school_name' => ['required', 'string', 'max:255'],
            'school_address_line_1' => ['nullable', 'string', 'max:255'],
            'school_address_line_2' => ['nullable', 'string', 'max:255'],
            'school_barangay' => ['nullable', 'string', 'max:255'],
            'school_city_municipality' => ['nullable', 'string', 'max:255'],
            'school_province' => ['nullable', 'string', 'max:255'],
            'school_region' => ['nullable', 'string', 'max:255'],
            'school_postal_code' => ['nullable', 'string', 'max:20'],
            'school_email' => ['nullable', 'email', 'max:255'],
            'school_contact_number' => ['nullable', 'string', 'max:255'],
            'school_motto' => ['nullable', 'string', 'max:255'],
            'school_website' => ['nullable', 'url', 'max:255'],
            'school_director' => ['nullable', 'string', 'max:255'],
            'domain' => [
                'required', 'string', 'max:255',
                'regex:/^(?=.{1,253}$)(?!-)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/',
                Rule::notIn($centralDomains),
                Rule::unique(Domain::class, 'domain')->ignore($tenant->domains()->value('id')),
            ],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['domain' => Str::lower(trim((string) $this->input('domain')))]);
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'domain.regex' => 'The domain must be a valid hostname without a scheme, port, or path.',
            'domain.not_in' => 'The central application domain cannot be assigned to a school.',
        ];
    }
}

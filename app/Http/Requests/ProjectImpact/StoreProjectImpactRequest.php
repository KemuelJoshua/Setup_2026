<?php

namespace App\Http\Requests\ProjectImpact;

use App\Models\ProjectImpact;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectImpactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', ProjectImpact::class) === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'source_sheet' => ['required', 'string', 'max:255'],
            'record_number' => ['nullable', 'string', 'max:255'],
            'project_title' => ['required', 'string', 'max:10000'],
            'proponent' => ['nullable', 'string', 'max:10000'],
            'classification' => ['nullable', 'string', 'max:255'],
            'sub_classification' => ['nullable', 'string', 'max:255'],
            'ip_type' => ['nullable', 'string', 'max:255'],
            'field_of_technology' => ['nullable', 'string', 'max:255'],
            'program_intervention' => ['nullable', 'string', 'max:10000'],
            'amount_assistance' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'date_assistance' => ['nullable', 'date'],
            'project_status' => ['nullable', 'string', 'max:255'],
            'date_completed' => ['nullable', 'date'],
            'readiness_before' => ['nullable', 'string', 'max:255'],
            'readiness_after' => ['nullable', 'string', 'max:255'],
            'other_interventions' => ['nullable', 'string', 'max:10000'],
            'revenue_amount' => ['nullable', 'string', 'max:10000'],
            'technology_commercialized' => ['nullable', 'string', 'max:10000'],
            'jobs_created' => ['nullable', 'string', 'max:10000'],
            'investment_leveraged' => ['nullable', 'string', 'max:10000'],
            'efficiency_improved' => ['nullable', 'string', 'max:10000'],
            'communities_served' => ['nullable', 'string', 'max:10000'],
            'priority_sectors_benefited' => ['nullable', 'string', 'max:10000'],
            'ip_assets_utilized' => ['nullable', 'string', 'max:10000'],
            'spin_offs_formed' => ['nullable', 'string', 'max:10000'],
            'human_capital_developed' => ['nullable', 'string', 'max:10000'],
            'other_impacts' => ['nullable', 'string', 'max:10000'],
            'impact_narrative' => ['nullable', 'string', 'max:50000'],
        ];
    }
}

<?php

namespace App\Http\Requests\StartupTracking;

use App\Models\StartupTracking;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStartupTrackingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', StartupTracking::class) === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:255'],
            'program' => ['required', 'string', 'max:255'],
            'project_title' => ['required', 'string', 'max:5000'],
            'proponent_name' => ['nullable', 'string', 'max:5000'],
            'contact_details' => ['nullable', 'string', 'max:5000'],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'class' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'promotional_assistance' => ['nullable', 'string', 'max:5000'],
            'revenue_growth' => ['nullable', 'string', 'max:5000'],
            'jobs_created' => ['nullable', 'string', 'max:5000'],
            'investments_attracted' => ['nullable', 'string', 'max:5000'],
            'market_reach' => ['nullable', 'string', 'max:5000'],
            'high_tech_exports' => ['nullable', 'string', 'max:5000'],
            'social_impact' => ['nullable', 'string', 'max:5000'],
            'next_possible_intervention' => ['nullable', 'string', 'max:5000'],
        ];
    }
}

<?php

namespace App\Http\Requests\Central;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ! tenancy()->initialized;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var Category $category */
        $category = $this->route('category');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique(Category::class, 'name')->ignore($category)],
            'is_active' => ['required', 'boolean'],
        ];
    }
}

<?php

namespace App\Actions\Categories;

use App\Models\Category;
use Illuminate\Validation\ValidationException;

class DeleteCategory
{
    public function execute(Category $category): void
    {
        if ($category->tenants()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'This category cannot be deleted while schools are assigned to it.',
            ]);
        }

        $category->delete();
    }
}

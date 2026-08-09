<?php

namespace App\Actions\Categories;

use App\Models\Category;

class UpdateCategory
{
    /** @param array{name: string, is_active: bool} $data */
    public function execute(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->refresh();
    }
}

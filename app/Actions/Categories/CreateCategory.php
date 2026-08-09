<?php

namespace App\Actions\Categories;

use App\Models\Category;

class CreateCategory
{
    /** @param array{name: string, is_active: bool} $data */
    public function execute(array $data): Category
    {
        return Category::query()->create($data);
    }
}

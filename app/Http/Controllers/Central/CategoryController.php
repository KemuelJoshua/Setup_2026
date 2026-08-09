<?php

namespace App\Http\Controllers\Central;

use App\Actions\Categories\CreateCategory;
use App\Actions\Categories\DeleteCategory;
use App\Actions\Categories\IndexCategory;
use App\Actions\Categories\UpdateCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\StoreCategoryRequest;
use App\Http\Requests\Central\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request, IndexCategory $indexCategory): Response
    {
        $perPage = $request->integer('per_page', 15);
        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        $categories = $indexCategory->execute($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn (Category $category): array => [
                'id' => $category->getKey(),
                'name' => $category->name,
                'is_active' => $category->is_active,
                'tenants_count' => $category->tenants_count,
            ]);

        return Inertia::render('central/categories/Index', [
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    public function store(StoreCategoryRequest $request, CreateCategory $createCategory): RedirectResponse
    {
        $createCategory->execute($request->validated());

        return redirect()
            ->to(route('central.categories.index', absolute: false))
            ->with('success', 'Category created successfully.');
    }

    public function update(
        UpdateCategoryRequest $request,
        Category $category,
        UpdateCategory $updateCategory,
    ): RedirectResponse {
        $updateCategory->execute($category, $request->validated());

        return redirect()
            ->to(route('central.categories.index', absolute: false))
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category, DeleteCategory $deleteCategory): RedirectResponse
    {
        $deleteCategory->execute($category);

        return redirect()
            ->to(route('central.categories.index', absolute: false))
            ->with('success', 'Category deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $schools = Tenant::query()
            ->select([
                'id',
                'category_id',
                'school_code',
                'school_name',
                'school_address',
                'school_address_line_1',
                'school_address_line_2',
                'school_barangay',
                'school_city_municipality',
                'school_province',
                'school_region',
                'school_postal_code',
                'school_motto',
                'data',
            ])
            ->whereHas('domains')
            ->with([
                'domains' => fn (HasMany $query): HasMany => $query
                    ->select(['id', 'tenant_id', 'domain'])
                    ->oldest('id'),
            ])
            ->orderBy('school_name')
            ->get()
            ->filter(fn (Tenant $tenant): bool => (bool) $tenant->is_active)
            ->map(function (Tenant $tenant) use ($request): array {
                $domain = $tenant->domains->firstOrFail()->domain;

                return [
                    'id' => $tenant->getTenantKey(),
                    'categoryId' => $tenant->category_id,
                    'code' => $tenant->school_code,
                    'name' => $tenant->school_name,
                    'address' => $tenant->school_address,
                    'motto' => $tenant->school_motto,
                    'url' => sprintf('%s://%s', $request->getScheme(), $domain),
                ];
            })
            ->values();

        $categories = Category::query()
            ->select(['id', 'name'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category): array => [
                'id' => $category->getKey(),
                'name' => $category->name,
                'schools' => $schools
                    ->where('categoryId', $category->getKey())
                    ->values(),
            ]);

        return Inertia::render('Welcome', [
            'categories' => $categories,
            'schools' => $schools,
        ]);
    }
}

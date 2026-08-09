<?php

namespace App\Http\Controllers\Central;

use App\Actions\Tenants\CreateTenant;
use App\Actions\Tenants\DeleteTenant;
use App\Actions\Tenants\IndexTenant;
use App\Actions\Tenants\UpdateTenant;
use App\Actions\Tenants\UpdateTenantStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\StoreTenantRequest;
use App\Http\Requests\Central\UpdateTenantRequest;
use App\Http\Requests\Central\UpdateTenantStatusRequest;
use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TenantController extends Controller
{
    public function index(Request $request, IndexTenant $indexTenant): Response
    {
        $perPage = $request->integer('per_page', 15);
        $filters = [
            'search' => $request->string('search')->trim()->toString() ?: null,
            'per_page' => in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15,
        ];

        $tenants = $indexTenant->execute($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn (Tenant $tenant): array => [
                'id' => $tenant->getTenantKey(),
                'category_id' => $tenant->category_id,
                'category' => $tenant->category?->only(['id', 'name']),
                'school_code' => $tenant->school_code,
                'school_name' => $tenant->school_name,
                'school_address' => $tenant->school_address,
                'school_address_line_1' => $tenant->school_address_line_1,
                'school_address_line_2' => $tenant->school_address_line_2,
                'school_barangay' => $tenant->school_barangay,
                'school_city_municipality' => $tenant->school_city_municipality,
                'school_province' => $tenant->school_province,
                'school_region' => $tenant->school_region,
                'school_postal_code' => $tenant->school_postal_code,
                'school_email' => $tenant->school_email,
                'school_contact_number' => $tenant->school_contact_number,
                'school_motto' => $tenant->school_motto,
                'school_website' => $tenant->school_website,
                'school_director' => $tenant->school_director,
                'domain' => $tenant->domains->first()?->domain,
                'is_active' => (bool) $tenant->is_active,
            ]);

        return Inertia::render('central/tenants/Index', [
            'tenants' => $tenants,
            'filters' => $filters,
            'categories' => Category::query()
                ->orderBy('name')
                ->get(['id', 'name', 'is_active']),
        ]);
    }

    public function store(StoreTenantRequest $request, CreateTenant $createTenant): RedirectResponse
    {
        $createTenant->execute($request->validated());

        return redirect()
            ->to(route('central.tenants.index', absolute: false))
            ->with('success', 'School created successfully.');
    }

    public function update(
        UpdateTenantRequest $request,
        Tenant $tenant,
        UpdateTenant $updateTenant,
    ): RedirectResponse {
        $updateTenant->execute($tenant, $request->validated());

        return redirect()
            ->to(route('central.tenants.index', absolute: false))
            ->with('success', 'School updated successfully.');
    }

    public function destroy(Tenant $tenant, DeleteTenant $deleteTenant): RedirectResponse
    {
        $deleteTenant->execute($tenant);

        return redirect()
            ->to(route('central.tenants.index', absolute: false))
            ->with('success', 'School deleted successfully.');
    }

    public function updateStatus(
        UpdateTenantStatusRequest $request,
        Tenant $tenant,
        UpdateTenantStatus $updateTenantStatus,
    ): RedirectResponse {
        $updateTenantStatus->execute($tenant, $request->boolean('is_active'));

        return redirect()
            ->to(route('central.tenants.index', absolute: false))
            ->with('success', 'School status updated successfully.');
    }
}

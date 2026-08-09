<?php

namespace App\Http\Controllers;

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
                    'code' => $tenant->school_code,
                    'name' => $tenant->school_name,
                    'address' => $tenant->school_address,
                    'motto' => $tenant->school_motto,
                    'url' => sprintf('%s://%s', $request->getScheme(), $domain),
                ];
            })
            ->values();

        return Inertia::render('Welcome', ['schools' => $schools]);
    }
}

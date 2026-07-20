<?php

namespace App\Http\Controllers\Admin\RolesAndPermissions;

use App\Actions\Roles\CreateRole;
use App\Actions\Roles\DeleteRole;
use App\Actions\Roles\IndexRole;
use App\Actions\Roles\UpdateRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\RolesAndPermissions\StoreRoleRequest;
use App\Http\Requests\RolesAndPermissions\UpdateRoleRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request, IndexRole $action): Response
    {
        Gate::authorize('admin view roles');

        $search = $request->string('search')->trim()->toString();
        $guard = $request->string('guard', 'all')->trim()->toString();

        $roles = $action->execute($request)
            ->paginate($request->integer('per_page', 10))
            ->withQueryString()
            ->through(fn (Role $role): array => [
                'id' => $role->getKey(),
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permission_ids' => $role->permissions->modelKeys(),
            ]);

        return Inertia::render('admin/settings/RolesAndPermissions/Index', [
            'roles' => $roles,
            'guards' => Role::query()
                ->select('guard_name')
                ->distinct()
                ->orderBy('guard_name')
                ->pluck('guard_name'),
            'permissions' => Permission::query()
                ->select(['id', 'name', 'guard_name'])
                ->where('name', 'like', '%admin%')
                ->orderBy('name')
                ->get()
                ->map(fn (Permission $permission): array => [
                    'id' => $permission->getKey(),
                    'name' => Str::of($permission->name)
                        ->replace('admin', '')
                        ->trim()
                        ->toString(),
                    'guard_name' => $permission->guard_name,
                    'category' => Str::of($permission->name)
                        ->afterLast(' ')
                        ->headline()
                        ->toString(),
                ]),
            'filters' => [
                'search' => $search,
                'guard' => $guard,
            ],
        ]);
    }

    public function store(StoreRoleRequest $request, CreateRole $createRole): RedirectResponse
    {
        Gate::authorize('admin create roles');

        $createRole->execute($request->validated());

        return redirect()
            ->route('admin.settings.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function update(UpdateRoleRequest $request, Role $role, UpdateRole $updateRole): RedirectResponse
    {
        Gate::authorize('admin update roles');

        $updateRole->execute($role, $request->validated());

        return redirect()
            ->route('admin.settings.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role, DeleteRole $deleteRole): RedirectResponse
    {
        Gate::authorize('admin delete roles');

        $deleteRole->execute($role);

        return redirect()
            ->route('admin.settings.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}

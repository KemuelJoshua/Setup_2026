<?php

namespace App\Http\Controllers\RolesAndPermissions;

use App\Actions\Roles\CreateRole;
use App\Actions\Roles\DeleteRole;
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
    public function index(Request $request): Response
    {
        Gate::authorize('view roles');

        $search = $request->string('search')->trim()->toString();
        $guard = $request->string('guard', 'all')->trim()->toString();

        $roles = Role::query()
            ->select(['id', 'name', 'guard_name'])
            ->with('permissions:id')
            ->when($search !== '', fn (Builder $query) => $query
                ->where(fn (Builder $query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('guard_name', 'like', "%{$search}%")))
            ->when($guard !== 'all', fn (Builder $query) => $query->where('guard_name', $guard))
            ->where('name', '!=', 'Superadmin')
            ->where('name', '!=', 'Student')
            ->where('name', '!=', 'Student')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Role $role): array => [
                'id' => $role->getKey(),
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permission_ids' => $role->permissions->modelKeys(),
            ]);

        return Inertia::render('RolesAndPermissions/Index', [
            'roles' => $roles,
            'guards' => Role::query()
                ->select('guard_name')
                ->distinct()
                ->orderBy('guard_name')
                ->pluck('guard_name'),
            'permissions' => Permission::query()
                ->select(['id', 'name', 'guard_name'])
                ->orderBy('name')
                ->get()
                ->map(fn (Permission $permission): array => [
                    'id' => $permission->getKey(),
                    'name' => $permission->name,
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
        Gate::authorize('create roles');

        $createRole->execute($request->validated());

        return redirect()
            ->route('administration.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function update(UpdateRoleRequest $request, Role $role, UpdateRole $updateRole): RedirectResponse
    {
        Gate::authorize('update roles');

        $updateRole->execute($role, $request->validated());

        return redirect()
            ->route('administration.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role, DeleteRole $deleteRole): RedirectResponse
    {
        Gate::authorize('delete roles');

        $deleteRole->execute($role);

        return redirect()
            ->route('administration.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}

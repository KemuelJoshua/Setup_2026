<?php

namespace App\Http\Controllers\Admin\Users;

use App\Actions\Roles\IndexRole;
use App\Actions\User\CreateUserAction;
use App\Actions\User\DestroyUserAction;
use App\Actions\User\IndexUserAction;
use App\Actions\User\UpdateUserAction;
use App\Data\Users\UserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(
        Request $request, 
        IndexUserAction $action,
        IndexRole $IndexRoleAction): Response
    {
        $filters = [
            'per_page' => $request->input('per_page', 15),
            'search' => $request->string('search')->trim()->toString() ?: null,
        ];

        $users = $action->execute($filters);
        $roles = $IndexRoleAction->execute();

        return Inertia::render('admin/users/Index', [
            'users' => $users->through(fn (User $user): array => [
                'id' => $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles
                    ->pluck('name')
                    ->values()
                    ->all(),
            ]),
            'roles' => $roles
                    ->get()
                    ->map(fn (Role $role): array => [
                        'id' => $role->getKey(),
                        'name' => $role->name,
                    ]),
            'filters' => $request->only(['per_page', 'search']),
        ]);
    }

    public function store(
        StoreUserRequest $request,
        CreateUserAction $createUser,
    ): RedirectResponse {
        $user = $createUser->execute(UserData::formStoreRequest($request));

        $this->syncRole($user, $request->validated('role_name'));

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function update(
        UpdateUserRequest $request,
        User $user,
        UpdateUserAction $updateUser,
    ): RedirectResponse {
        $updatedUser = $updateUser->execute(
            id: (string) $user->getKey(),
            data: UserData::fromUpdateRequest($request),
        );

        $this->syncRole($updatedUser, $request->validated('role_name'));

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user, DestroyUserAction $destroyUser): RedirectResponse
    {
        $destroyUser->execute((string) $user->getKey());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    private function syncRole(User $user, ?string $roleName): void
    {
        if ($roleName === null) {
            $user->syncRoles([]);

            return;
        }

        $user->syncRoles([$roleName]);
    }
}

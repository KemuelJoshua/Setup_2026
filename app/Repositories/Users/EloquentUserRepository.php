<?php

namespace App\Repositories\Users;

use App\Data\Users\UserData;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function paginate(
        int $perPage = 15,
        ?string $search = null
    ): LengthAwarePaginator {
        $query = User::query()
            ->select(['id', 'name', 'email'])
            ->with('roles:id,name,guard_name')
            ->latest('id');

        if ($search) {
            $query->where(function ($query) use ($search): void {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): ?UserData
    {
        $user = User::find($id);

        return $user ? new UserData(
            name: $user->name,
            email: $user->email,
        ) : null;
    }

    public function create(UserData $data): User
    {
        return User::create($data->toArray());
    }

    public function update(string $id, UserData $data): User
    {
        $user = User::findOrFail($id);
        $user->update(array_filter(
            $data->toArray(),
            fn (mixed $value): bool => $value !== null,
        ));

        return $user;
    }

    public function delete(string $id): bool
    {
        $user = User::find($id);

        return $user ? $user->delete() : false;
    }
}

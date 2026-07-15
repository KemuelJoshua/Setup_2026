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
        $query = User::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?UserData
    {
        $user = User::find($id);

        return $user ? new UserData(
            name: $user->name,
            email: $user->email,
        ) : null;
    }

    public function create(UserData $data): UserData
    {
        $user = User::create($data->toArray());

        return new UserData(
            name: $user->name,
            email: $user->email,
        );
    }

    public function update(string $id, UserData $data): UserData
    {
        $user = User::find($id);
        $user->update(array_filter(
            $data->toArray(),
            fn (mixed $value): bool => $value !== null,
        ));

        return new UserData(
            name: $user->name,
            email: $user->email,
        );
    }

    public function delete(string $id): bool
    {
        $user = User::find($id);

        return $user ? $user->delete() : false;
    }
}

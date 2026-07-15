<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Data\Users\UserData;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<User>
     */
    public function paginate(
        int $perPage = 15,
        ?string $search = null
    ): LengthAwarePaginator;

    public function findById(int $id): ?UserData;

    public function create(UserData $data): UserData;

    public function update(string $id, UserData $data): UserData;

    public function delete(string $id): bool;
}

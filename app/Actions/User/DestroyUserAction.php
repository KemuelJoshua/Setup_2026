<?php

namespace App\Actions\User;

use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DestroyUserAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $isDeleted = $this->userRepository->delete($id);

            return $isDeleted;
        });
    }
}

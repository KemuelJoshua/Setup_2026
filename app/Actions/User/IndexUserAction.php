<?php

namespace App\Actions\User;

use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class IndexUserAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(array $filters): LengthAwarePaginator
    {
        return $this->userRepository->paginate(
            perPage: $filters['per_page'] ?? 15,
            search: $filters['search'] ?? null,
        );
    }
}

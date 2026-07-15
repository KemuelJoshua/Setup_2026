<?php

namespace App\Actions\User;

use App\Data\Users\UserData;
use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(UserData $data): UserData
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepository->create(
                new UserData(
                    name: $data->name,
                    email: $data->email,
                    password: Hash::make($data->password),
                )
            );

            return $user;
        });
    }
}

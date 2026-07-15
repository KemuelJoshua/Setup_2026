<?php

namespace App\Actions\User;

use App\Data\Users\UserData;
use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final readonly class UpdateUserAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(string $id, UserData $data): UserData
    {
        return DB::transaction(function () use ($id, $data) {
            $user = $this->userRepository->update(
                id: $id,
                data: new UserData(
                    name: $data->name,
                    email: $data->email,
                    password: $data->password !== null
                        ? Hash::make($data->password)
                        : null,
                )
            );

            return $user;
        });
    }
}

<?php

namespace App\Data\Users;

use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;

final readonly class UserData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password = null,
    ) {}

    public static function formStoreRequest(StoreUserRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
        );
    }

    public static function fromUpdateRequest(
        UpdateUserRequest $request
    ): self {
        $validated = $request->validated();

        return new self(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}

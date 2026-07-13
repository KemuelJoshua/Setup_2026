<?php

namespace App\Actions\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateAdmin
{
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $admin = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $admin->assignRole('admin');

            return $admin;
        });
    }
}
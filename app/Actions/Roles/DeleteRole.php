<?php

namespace App\Actions\Roles;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DeleteRole
{
    public function execute(Role $role): void
    {
        DB::transaction(function () use ($role): void {
            $role->delete();
        });
    }
}

<?php

use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

test('it seeds role crud permissions idempotently', function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(PermissionSeeder::class);

    expect(Permission::query()
        ->where('guard_name', 'web')
        ->orderBy('name')
        ->pluck('name')
        ->all())
        ->toBe([
            'create roles',
            'delete roles',
            'update roles',
            'view roles',
        ]);
});

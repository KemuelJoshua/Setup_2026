<?php

namespace App\Listeners;

use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\Events\RevertedToCentralContext;
use Stancl\Tenancy\Events\TenancyBootstrapped;

class InitializePermissionCache
{
    public function __construct(private readonly PermissionRegistrar $permissionRegistrar) {}

    public function handle(TenancyBootstrapped|RevertedToCentralContext $event): void
    {
        $this->permissionRegistrar->initializeCache();

        if ($event instanceof TenancyBootstrapped) {
            $this->permissionRegistrar->cacheKey .= '.tenant.'.(string) tenant('id');
        }
    }
}

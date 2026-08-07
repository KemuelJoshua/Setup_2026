<?php

namespace App\Tenancy;

use Illuminate\Cache\CacheManager;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class CacheTenancyBootstrapper implements TenancyBootstrapper
{
    private ?string $originalPrefix = null;

    public function __construct(private readonly CacheManager $cache) {}

    public function bootstrap(Tenant $tenant): void
    {
        $this->originalPrefix ??= (string) config('cache.prefix', '');
        config()->set('cache.prefix', $this->originalPrefix.'tenant_'.$tenant->getTenantKey().'_');
        $this->cache->forgetDriver();
    }

    public function revert(): void
    {
        config()->set('cache.prefix', $this->originalPrefix);
        $this->cache->forgetDriver();
        $this->originalPrefix = null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /** @var list<string> */
    protected $fillable = [
        'school_code', 'school_name', 'school_address', 'school_email',
        'school_contact_number', 'school_logo', 'school_favicon',
        'school_motto', 'school_website', 'school_director', 'is_active',
    ];

    protected $attributes = ['is_active' => true];

    /** @return list<string> */
    public static function getCustomColumns(): array
    {
        return [
            'id', 'school_code', 'school_name', 'school_address', 'school_email',
            'school_contact_number', 'school_logo', 'school_favicon',
            'school_motto', 'school_website', 'school_director',
        ];
    }

    /** @return HasMany<Domain, $this> */
    public function domains(): HasMany
    {
        return $this->hasMany(config('tenancy.domain_model'), 'tenant_id');
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}

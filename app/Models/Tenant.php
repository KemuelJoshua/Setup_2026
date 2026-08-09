<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'category_id', 'school_code', 'school_name', 'school_address', 'school_email',
        'school_address_line_1', 'school_address_line_2', 'school_barangay',
        'school_city_municipality', 'school_province', 'school_region', 'school_postal_code',
        'school_contact_number', 'school_logo', 'school_favicon',
        'school_motto', 'school_website', 'school_director', 'is_active',
    ];

    protected $attributes = ['is_active' => true];

    /** @return list<string> */
    public static function getCustomColumns(): array
    {
        return [
            'id', 'category_id', 'school_code', 'school_name', 'school_address', 'school_email',
            'school_address_line_1', 'school_address_line_2', 'school_barangay',
            'school_city_municipality', 'school_province', 'school_region', 'school_postal_code',
            'school_contact_number', 'school_logo', 'school_favicon',
            'school_motto', 'school_website', 'school_director',
        ];
    }

    /** @return HasMany<Domain, $this> */
    public function domains(): HasMany
    {
        return $this->hasMany(config('tenancy.domain_model'), 'tenant_id');
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected function schoolAddress(): Attribute
    {
        return Attribute::get(function (?string $legacyAddress, array $attributes): ?string {
            $address = collect([
                $attributes['school_address_line_1'] ?? null,
                $attributes['school_address_line_2'] ?? null,
                $attributes['school_barangay'] ?? null,
                $attributes['school_city_municipality'] ?? null,
                $attributes['school_province'] ?? null,
                $attributes['school_region'] ?? null,
                $attributes['school_postal_code'] ?? null,
            ])->filter(fn (mixed $part): bool => filled($part))->implode(', ');

            return $address !== '' ? $address : $legacyAddress;
        });
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}

<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id', 'name', 'theme', 'ecommerce_enabled', 'header_layout',
        'logo_path', 'stripe_account_id', 'payment_gateway', 'plan',
    ];

    protected $casts = [
        'ecommerce_enabled' => 'boolean',
        'data' => 'array',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id', 'name', 'theme', 'ecommerce_enabled', 'header_layout',
            'logo_path', 'stripe_account_id', 'payment_gateway', 'plan',
        ];
    }
}

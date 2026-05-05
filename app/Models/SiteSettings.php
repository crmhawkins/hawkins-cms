<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    protected $fillable = [
        'site_name', 'site_url', 'theme', 'ecommerce_enabled',
        'payment_gateway', 'stripe_secret_key', 'stripe_webhook_secret',
        'logo_path', 'contact_email',
    ];

    protected $casts = [
        'ecommerce_enabled' => 'boolean',
        'stripe_secret_key' => 'encrypted',
        'stripe_webhook_secret' => 'encrypted',
    ];

    public static function instance(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'site_name' => config('app.name', 'Mi Sitio'),
                'site_url' => config('app.url'),
                'theme' => 'default',
                'ecommerce_enabled' => false,
                'payment_gateway' => 'none',
            ]
        );
    }
}

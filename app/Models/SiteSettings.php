<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    protected $fillable = [
        'site_name', 'site_url', 'theme', 'ecommerce_enabled',
        'payment_gateway', 'stripe_secret_key', 'stripe_webhook_secret',
        'logo_path', 'contact_email',
        'favicon_path','accent_color','font_heading','font_body',
        'google_analytics_code','custom_head_code','custom_body_code',
        'social_instagram','social_facebook','social_twitter','social_linkedin','social_youtube',
        'default_header_id','default_footer_id','maintenance_mode','maintenance_message',
    ];

    protected $casts = [
        'ecommerce_enabled' => 'boolean',
        'stripe_secret_key' => 'encrypted',
        'stripe_webhook_secret' => 'encrypted',
        'maintenance_mode' => 'boolean',
        'default_header_id' => 'integer',
        'default_footer_id' => 'integer',
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

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    protected $fillable = [
        'name','type','bg_color','text_color','link_color','border_color',
        'logo_path','logo_text','tagline',
        'phone','email','address',
        'social_instagram','social_facebook','social_twitter','social_linkedin','social_youtube',
        'copyright_text',
        'show_newsletter','newsletter_title','newsletter_placeholder',
        'menu_columns','is_default',
    ];

    protected $casts = [
        'show_newsletter' => 'boolean',
        'is_default' => 'boolean',
        'menu_columns' => 'array',
    ];

    public static function getDefault(): self
    {
        return static::where('is_default', true)->first()
            ?? static::first()
            ?? static::create([
                'name' => 'Footer principal',
                'type' => 'classic',
                'bg_color' => '#111111',
                'text_color' => '#ffffff',
                'link_color' => '#c9a96e',
                'border_color' => '#333333',
                'copyright_text' => '© ' . date('Y') . ' Hawkins CMS. Todos los derechos reservados.',
                'is_default' => true,
            ]);
    }
}

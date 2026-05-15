<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Header extends Model
{
    protected $fillable = [
        'name','type','layout','logo_path','logo_text','logo_height',
        'bg_color','text_color','hover_color','active_color',
        'sticky','transparent_on_top',
        'cta_text','cta_url','cta_bg_color','cta_text_color',
        'phone','email',
        'social_instagram','social_facebook','social_twitter','social_linkedin','social_youtube',
        'show_search','show_social','is_default',
    ];

    protected $casts = [
        'sticky' => 'boolean',
        'transparent_on_top' => 'boolean',
        'show_search' => 'boolean',
        'show_social' => 'boolean',
        'is_default' => 'boolean',
        'logo_height' => 'integer',
    ];

    public static function getDefault(): self
    {
        return static::where('is_default', true)->first()
            ?? static::first()
            ?? static::create([
                'name' => 'Header principal',
                'type' => 'classic',
                'layout' => 'logo_left',
                'bg_color' => '#ffffff',
                'text_color' => '#111111',
                'hover_color' => '#c9a96e',
                'active_color' => '#c9a96e',
                'is_default' => true,
            ]);
    }

    public static function getInstance(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            ['layout' => 'split', 'bg_color' => '#ffffff', 'text_color' => '#000000']
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Header extends Model
{
    protected $fillable = ['layout', 'logo_path', 'bg_color', 'text_color'];

    public static function getInstance(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            ['layout' => 'split', 'bg_color' => '#ffffff', 'text_color' => '#000000']
        );
    }
}

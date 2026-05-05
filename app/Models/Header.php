<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Header extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'layout', 'logo_path', 'bg_color', 'text_color'];

    public static function forTenant(string $tenantId): self
    {
        return static::firstOrCreate(
            ['tenant_id' => $tenantId],
            ['layout' => 'split', 'bg_color' => '#ffffff', 'text_color' => '#000000']
        );
    }
}

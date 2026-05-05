<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'title', 'slug', 'status', 'seo_meta', 'published_at'];

    protected $casts = [
        'seo_meta' => 'array',
        'published_at' => 'datetime',
    ];

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class)->orderBy('sort');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}

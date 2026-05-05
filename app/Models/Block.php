<?php
namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Block extends Model
{
    use BelongsToTenant;

    protected $fillable = ['page_id', 'tenant_id', 'type', 'content', 'sort'];

    protected $casts = [
        'content' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(BlockRevision::class)->latest()->limit(20);
    }
}

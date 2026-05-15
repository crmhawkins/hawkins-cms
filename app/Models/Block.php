<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Block extends Model
{
    protected $fillable = [
        'page_id', 'type', 'content', 'sort',
        'bg_color','text_color','padding_top','padding_bottom','padding_x',
        'margin_top','margin_bottom','container_width',
        'separator_top','separator_bottom','separator_color',
        'full_width','css_class','custom_css',
    ];

    protected $casts = [
        'content' => 'array',
        'padding_top' => 'integer',
        'padding_bottom' => 'integer',
        'padding_x' => 'integer',
        'margin_top' => 'integer',
        'margin_bottom' => 'integer',
        'full_width' => 'boolean',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(BlockRevision::class)->latest();
    }
}

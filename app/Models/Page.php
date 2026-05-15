<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'status', 'seo_meta', 'published_at',
        'meta_title', 'meta_description', 'og_image', 'meta_robots',
        'header_variant', 'footer_variant',
        'header_id', 'footer_id', 'custom_css', 'custom_js',
    ];

    protected $casts = [
        'seo_meta' => 'array',
        'published_at' => 'datetime',
    ];

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class)->orderBy('sort');
    }

    public function header(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Header::class);
    }

    public function footer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Footer::class);
    }

    public function resolvedHeader(): Header
    {
        return $this->header ?? Header::getDefault();
    }

    public function resolvedFooter(): Footer
    {
        return $this->footer ?? Footer::getDefault();
    }

    public function seoTitle(): string
    {
        return $this->meta_title ?: $this->title;
    }

    public function seoDescription(): string
    {
        return $this->meta_description ?: '';
    }
}

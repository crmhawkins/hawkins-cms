<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'category_id', 'title', 'slug', 'excerpt', 'body',
        'featured_image', 'status', 'published_at',
        'meta_title', 'meta_description', 'og_image', 'meta_robots',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published')
            ->where(fn ($q2) => $q2->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function seoTitle(): string
    {
        return $this->meta_title ?: $this->title;
    }

    public function seoDescription(): string
    {
        return $this->meta_description ?: ($this->excerpt ?: '');
    }
}

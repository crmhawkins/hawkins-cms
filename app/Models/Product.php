<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'name', 'slug', 'description', 'price', 'compare_price',
        'stock', 'track_stock', 'images', 'status', 'metadata',
    ];

    protected $casts = [
        'images' => 'array',
        'metadata' => 'array',
        'track_stock' => 'boolean',
        'price' => 'integer',
        'compare_price' => 'integer',
        'stock' => 'integer',
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function getPriceFormattedAttribute(): string
    {
        return number_format($this->price / 100, 2, ',', '.') . ' €';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}

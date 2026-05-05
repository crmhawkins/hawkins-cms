<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'session_id', 'user_id', 'items', 'expires_at',
    ];

    protected $casts = [
        'items' => 'array',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addItem(int $productId, ?int $variantId, int $qty, int $price): void
    {
        $items = $this->items ?? [];
        $found = false;

        foreach ($items as &$item) {
            if (($item['product_id'] ?? null) === $productId
                && ($item['variant_id'] ?? null) === $variantId) {
                $item['qty'] += $qty;
                $found = true;
                break;
            }
        }
        unset($item);

        if (! $found) {
            $items[] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'qty' => $qty,
                'price_at_add' => $price,
            ];
        }

        $this->items = $items;
        $this->save();
    }

    public function total(): int
    {
        return collect($this->items ?? [])->sum(fn ($item) => ($item['qty'] ?? 0) * ($item['price_at_add'] ?? 0));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }
}

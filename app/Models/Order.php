<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'order_number', 'status', 'items',
        'subtotal', 'tax_amount', 'total', 'currency',
        'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'payment_gateway', 'payment_id',
        'stripe_payment_intent_id', 'refund_id', 'notes',
        'paid_at', 'shipped_at',
    ];

    protected $casts = [
        'items' => 'array',
        'shipping_address' => 'array',
        'subtotal' => 'integer',
        'tax_amount' => 'integer',
        'total' => 'integer',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . strtoupper(Str::random(8));
        } while (self::withoutGlobalScopes()->where('order_number', $number)->exists());

        return $number;
    }

    public function totalFormatted(): string
    {
        return number_format($this->total / 100, 2, ',', '.') . ' €';
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }
}

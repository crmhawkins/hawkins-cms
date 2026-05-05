<?php

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Support\Tax;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('product can be created', function () {
    $product = Product::create([
        'name' => 'Vestido Rosa',
        'slug' => 'vestido-rosa',
        'price' => 4999,
        'stock' => 10,
        'status' => 'active',
    ]);

    expect($product->priceFormatted)->toBe('49,99 €');
});

test('cart calculates total correctly', function () {
    $cart = Cart::create([
        'session_id' => 'sess-test',
        'items' => [],
    ]);

    $cart->addItem(1, null, 2, 1000);
    $cart->addItem(2, null, 3, 500);

    expect($cart->fresh()->total())->toBe(2 * 1000 + 3 * 500);
});

test('order generates unique order number', function () {
    $a = Order::generateOrderNumber();
    $b = Order::generateOrderNumber();

    expect($a)->toStartWith('ORD-');
    expect($b)->toStartWith('ORD-');
    expect($a)->not->toBe($b);
});

test('tax helper calculates 21% IVA', function () {
    expect(Tax::apply(1000))->toBe(1210);
    expect(Tax::calculate(1000))->toBe(210);
});

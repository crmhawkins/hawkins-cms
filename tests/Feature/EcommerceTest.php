<?php

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tenant;
use App\Support\Tax;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('product can be created for tenant', function () {
    $tenant = Tenant::create([
        'id' => 'tenant-shop-' . uniqid(),
        'name' => 'Shop Tenant',
        'ecommerce_enabled' => true,
    ]);

    $product = Product::create([
        'tenant_id' => $tenant->id,
        'name' => 'Vestido Rosa',
        'slug' => 'vestido-rosa',
        'price' => 4999,
        'stock' => 10,
        'status' => 'active',
    ]);

    expect($product->priceFormatted)->toBe('49,99 €');
    expect($product->tenant_id)->toBe($tenant->id);
});

test('cart calculates total correctly', function () {
    $tenant = Tenant::create([
        'id' => 'tenant-cart-' . uniqid(),
        'name' => 'Cart Tenant',
    ]);

    $cart = Cart::create([
        'tenant_id' => $tenant->id,
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

test('product is isolated between tenants', function () {
    $tenantA = Tenant::create(['id' => 'tenant-a-' . uniqid(), 'name' => 'A']);
    $tenantB = Tenant::create(['id' => 'tenant-b-' . uniqid(), 'name' => 'B']);

    Product::create([
        'tenant_id' => $tenantA->id,
        'name' => 'Solo A',
        'slug' => 'solo-a-' . uniqid(),
        'price' => 100,
        'status' => 'active',
    ]);

    $bProducts = Product::where('tenant_id', $tenantB->id)->get();
    expect($bProducts)->toHaveCount(0);

    $aProducts = Product::where('tenant_id', $tenantA->id)->get();
    expect($aProducts)->toHaveCount(1);
});

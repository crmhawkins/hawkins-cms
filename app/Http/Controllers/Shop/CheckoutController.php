<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tenant;
use App\Services\Payments\PaymentGatewayFactory;
use App\Support\Tax;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function show(Request $request)
    {
        $cart = $this->resolveCart($request);
        if (empty($cart->items)) {
            return redirect()->route('shop.cart')->with('status', 'Tu carrito está vacío');
        }
        $subtotal = $cart->total();
        $tax = Tax::calculate($subtotal);
        $total = $subtotal + $tax;

        return view('shop.checkout', compact('cart', 'subtotal', 'tax', 'total'));
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
        ]);

        $cart = $this->resolveCart($request);
        if (empty($cart->items)) {
            return redirect()->route('shop.cart')->with('status', 'Tu carrito está vacío');
        }

        $tenantId = function_exists('tenant') && tenant() ? tenant('id') : ($cart->tenant_id ?? null);
        $tenant = $tenantId ? Tenant::find($tenantId) : null;

        $items = collect($cart->items)->map(function ($item) {
            $product = Product::find($item['product_id'] ?? null);
            return [
                'product_id' => $item['product_id'] ?? null,
                'variant_id' => $item['variant_id'] ?? null,
                'name' => $product?->name ?? 'Producto',
                'qty' => (int) ($item['qty'] ?? 1),
                'price' => (int) ($item['price_at_add'] ?? 0),
            ];
        })->values()->all();

        $subtotal = collect($items)->sum(fn ($i) => $i['qty'] * $i['price']);
        $tax = Tax::calculate($subtotal);
        $total = $subtotal + $tax;

        $order = Order::create([
            'tenant_id' => $tenantId,
            'user_id' => auth()->id(),
            'order_number' => Order::generateOrderNumber(),
            'status' => 'pending',
            'items' => $items,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total' => $total,
            'currency' => 'EUR',
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'payment_gateway' => $tenant?->payment_gateway ?? 'none',
        ]);

        if (! $tenant || $tenant->payment_gateway === 'none') {
            return redirect()->route('shop.success', ['order' => $order->order_number]);
        }

        $gateway = PaymentGatewayFactory::for($tenant);
        $session = $gateway->createCheckoutSession($order);

        $order->update(['payment_id' => $session->id]);

        // Clear cart
        $cart->update(['items' => []]);

        return redirect()->away($session->url);
    }

    public function success(string $orderNumber)
    {
        $order = Order::withoutGlobalScopes()->where('order_number', $orderNumber)->firstOrFail();

        return view('shop.success', compact('order'));
    }

    public function cancel()
    {
        return view('shop.cancel');
    }

    private function resolveCart(Request $request): Cart
    {
        $sessionId = $request->session()->getId();
        $cart = Cart::where('session_id', $sessionId)->first();
        if (! $cart) {
            $cart = Cart::create([
                'session_id' => $sessionId,
                'user_id' => auth()->id(),
                'items' => [],
            ]);
        }
        return $cart;
    }
}

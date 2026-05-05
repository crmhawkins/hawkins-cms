<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Support\Tax;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request)
    {
        $cart = $this->resolveCart($request);
        $subtotal = $cart->total();
        $tax = Tax::calculate($subtotal);
        $total = $subtotal + $tax;

        return view('shop.cart', compact('cart', 'subtotal', 'tax', 'total'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'qty' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($data['product_id']);
        $variantId = isset($data['variant_id']) ? (int) $data['variant_id'] : null;
        $price = $variantId
            ? (\App\Models\ProductVariant::find($variantId)?->price_override ?? $product->price)
            : $product->price;
        $cart = $this->resolveCart($request);
        $cart->addItem(
            (int) $data['product_id'],
            $variantId,
            (int) $data['qty'],
            (int) $price,
        );

        return redirect()->route('shop.cart')->with('status', 'Producto añadido al carrito');
    }

    public function remove(Request $request)
    {
        $data = $request->validate([
            'index' => 'required|integer|min:0',
        ]);

        $cart = $this->resolveCart($request);
        $items = $cart->items ?? [];
        if (isset($items[$data['index']])) {
            array_splice($items, $data['index'], 1);
            $cart->items = $items;
            $cart->save();
        }

        return redirect()->route('shop.cart');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'index' => 'required|integer|min:0',
            'qty' => 'required|integer|min:1',
        ]);

        $cart = $this->resolveCart($request);
        $items = $cart->items ?? [];
        if (isset($items[$data['index']])) {
            $items[$data['index']]['qty'] = (int) $data['qty'];
            $cart->items = $items;
            $cart->save();
        }

        return redirect()->route('shop.cart');
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

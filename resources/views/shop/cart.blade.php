<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Tu carrito</h1>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    @if (empty($cart->items))
        <p>El carrito está vacío.</p>
    @else
        <table border="1" cellpadding="6">
            <thead>
                <tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th><th></th></tr>
            </thead>
            <tbody>
            @foreach ($cart->items as $i => $item)
                <tr>
                    <td>{{ \App\Models\Product::find($item['product_id'])?->name ?? 'Producto' }}</td>
                    <td>
                        <form method="POST" action="{{ route('shop.cart.update') }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="index" value="{{ $i }}">
                            <input type="number" name="qty" value="{{ $item['qty'] }}" min="1">
                            <button type="submit">Actualizar</button>
                        </form>
                    </td>
                    <td>{{ \App\Support\Tax::format($item['price_at_add']) }}</td>
                    <td>{{ \App\Support\Tax::format($item['qty'] * $item['price_at_add']) }}</td>
                    <td>
                        <form method="POST" action="{{ route('shop.cart.remove') }}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="index" value="{{ $i }}">
                            <button type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <p>Subtotal: {{ \App\Support\Tax::format($subtotal) }}</p>
        <p>IVA (21%): {{ \App\Support\Tax::format($tax) }}</p>
        <p><strong>Total: {{ \App\Support\Tax::format($total) }}</strong></p>

        <a href="{{ route('shop.checkout') }}">Ir al checkout</a>
    @endif
</body>
</html>

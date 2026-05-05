<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
</head>
<body>
    <h1>Finalizar compra</h1>

    <p>Subtotal: {{ \App\Support\Tax::format($subtotal) }}</p>
    <p>IVA (21%): {{ \App\Support\Tax::format($tax) }}</p>
    <p><strong>Total: {{ \App\Support\Tax::format($total) }}</strong></p>

    <form method="POST" action="{{ route('shop.checkout.submit') }}">
        @csrf
        <p>
            <label>Nombre<br>
                <input type="text" name="customer_name" required>
            </label>
        </p>
        <p>
            <label>Email<br>
                <input type="email" name="customer_email" required>
            </label>
        </p>
        <p>
            <label>Teléfono<br>
                <input type="text" name="customer_phone">
            </label>
        </p>
        <button type="submit">Pagar con Stripe</button>
    </form>
</body>
</html>

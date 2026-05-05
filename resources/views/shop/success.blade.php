<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido confirmado</title>
</head>
<body>
    <h1>¡Pedido confirmado!</h1>
    <p>Gracias, {{ $order->customer_name }}.</p>
    <p>Tu número de pedido es: <strong>{{ $order->order_number }}</strong></p>
    <p>Total: {{ $order->totalFormatted() }}</p>
    <p>Recibirás un email de confirmación en {{ $order->customer_email }}.</p>
    <a href="{{ route('home') }}">Volver a la tienda</a>
</body>
</html>

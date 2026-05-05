@extends('themes.sanzahra.layouts.app')

@section('title', 'Carrito')

@section('content')
<div style="max-width:900px;margin:3rem auto;padding:0 1.5rem;">

    <h1 style="font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:400;margin-bottom:2rem;">Tu carrito</h1>

    @if (session('status'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:.75rem 1rem;border-radius:4px;margin-bottom:1.5rem;">
            {{ session('status') }}
        </div>
    @endif

    @if (empty($cart->items))
        <p style="color:#666;font-size:1.05rem;">El carrito está vacío.</p>
        <a href="{{ url('/') }}" style="display:inline-block;margin-top:1rem;color:#333;text-decoration:underline;">Volver a la tienda</a>
    @else
        {{-- Table for md+ --}}
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:.95rem;">
                <thead>
                    <tr style="border-bottom:2px solid #e5e7eb;">
                        <th style="text-align:left;padding:.75rem .5rem;font-weight:500;font-family:'Montserrat',sans-serif;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:#6b7280;">Producto</th>
                        <th style="text-align:center;padding:.75rem .5rem;font-weight:500;font-family:'Montserrat',sans-serif;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:#6b7280;">Cantidad</th>
                        <th style="text-align:right;padding:.75rem .5rem;font-weight:500;font-family:'Montserrat',sans-serif;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:#6b7280;">Precio</th>
                        <th style="text-align:right;padding:.75rem .5rem;font-weight:500;font-family:'Montserrat',sans-serif;font-size:.8rem;text-transform:uppercase;letter-spacing:.05em;color:#6b7280;">Subtotal</th>
                        <th style="padding:.75rem .5rem;"></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($cart->items as $i => $item)
                    @php $productName = \App\Models\Product::find($item['product_id'])?->name ?? 'Producto'; @endphp
                    <tr style="border-bottom:1px solid #f3f4f6;">
                        <td style="padding:.875rem .5rem;color:#111;">
                            {{ $productName }}
                            @if(!empty($item['variant_id']))
                                <span style="font-size:.8rem;color:#6b7280;display:block;">Variante #{{ $item['variant_id'] }}</span>
                            @endif
                        </td>
                        <td style="padding:.875rem .5rem;text-align:center;">
                            <form method="POST" action="{{ route('shop.cart.update') }}" style="display:inline-flex;align-items:center;gap:.25rem;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="index" value="{{ $i }}">
                                <input type="number" name="qty" value="{{ $item['qty'] }}" min="1"
                                    style="width:60px;border:1px solid #d1d5db;border-radius:4px;padding:.25rem .5rem;text-align:center;font-size:.95rem;">
                                <button type="submit"
                                    style="background:#f9fafb;border:1px solid #d1d5db;border-radius:4px;padding:.25rem .5rem;font-size:.75rem;cursor:pointer;color:#374151;">↺</button>
                            </form>
                        </td>
                        <td style="padding:.875rem .5rem;text-align:right;color:#374151;">{{ \App\Support\Tax::format($item['price_at_add']) }}</td>
                        <td style="padding:.875rem .5rem;text-align:right;font-weight:500;color:#111;">{{ \App\Support\Tax::format($item['qty'] * $item['price_at_add']) }}</td>
                        <td style="padding:.875rem .5rem;text-align:right;">
                            <form method="POST" action="{{ route('shop.cart.remove') }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="index" value="{{ $i }}">
                                <button type="submit"
                                    style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:1.1rem;line-height:1;"
                                    title="Eliminar">×</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div style="margin-top:2rem;display:flex;justify-content:flex-end;">
            <div style="min-width:280px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:1.25rem 1.5rem;">
                <div style="display:flex;justify-content:space-between;margin-bottom:.5rem;font-size:.95rem;color:#374151;">
                    <span>Subtotal</span>
                    <span>{{ \App\Support\Tax::format($subtotal) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:.75rem;font-size:.95rem;color:#374151;">
                    <span>IVA (21%)</span>
                    <span>{{ \App\Support\Tax::format($tax) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:1.1rem;font-weight:600;color:#111;border-top:1px solid #e5e7eb;padding-top:.75rem;">
                    <span>Total</span>
                    <span>{{ \App\Support\Tax::format($total) }}</span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div style="margin-top:1.5rem;display:flex;gap:1rem;justify-content:flex-end;flex-wrap:wrap;">
            <form method="POST" action="{{ route('shop.cart.clear') }}" onsubmit="return confirm('¿Vaciar el carrito?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    style="padding:.6rem 1.25rem;border:1px solid #d1d5db;background:#fff;border-radius:4px;cursor:pointer;font-family:'Montserrat',sans-serif;font-size:.875rem;color:#374151;">
                    Vaciar carrito
                </button>
            </form>
            <a href="{{ route('shop.checkout') }}"
                style="display:inline-block;padding:.6rem 1.5rem;background:#111;color:#fff;text-decoration:none;border-radius:4px;font-family:'Montserrat',sans-serif;font-size:.875rem;letter-spacing:.03em;">
                Proceder al pago →
            </a>
        </div>
    @endif

</div>
@endsection

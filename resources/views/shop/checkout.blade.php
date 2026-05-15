@extends('themes.sanzahra.layouts.app')

@section('title', 'Finalizar compra')

@section('content')
<div style="max-width:900px;margin:3rem auto;padding:0 1.5rem;">

    <h1 style="font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:400;margin-bottom:2rem;">Finalizar compra</h1>

    <div style="display:flex;gap:2rem;flex-wrap:wrap;align-items:flex-start;">

        {{-- Form --}}
        <div style="flex:1;min-width:280px;">
            <h2 style="font-family:'Montserrat',sans-serif;font-size:.9rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;margin-bottom:1.25rem;">Datos de contacto</h2>

            <form method="POST" action="{{ route('shop.checkout.submit') }}">
                @csrf

                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-family:'Montserrat',sans-serif;font-size:.8rem;font-weight:500;color:#374151;margin-bottom:.35rem;">Nombre</label>
                    <input type="text" name="customer_name" required
                        style="width:100%;border:1px solid #d1d5db;border-radius:4px;padding:.6rem .75rem;font-size:.95rem;color:#111;box-sizing:border-box;">
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-family:'Montserrat',sans-serif;font-size:.8rem;font-weight:500;color:#374151;margin-bottom:.35rem;">Correo electrónico</label>
                    <input type="email" name="customer_email" required
                        style="width:100%;border:1px solid #d1d5db;border-radius:4px;padding:.6rem .75rem;font-size:.95rem;color:#111;box-sizing:border-box;">
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label style="display:block;font-family:'Montserrat',sans-serif;font-size:.8rem;font-weight:500;color:#374151;margin-bottom:.35rem;">Teléfono</label>
                    <input type="text" name="customer_phone"
                        style="width:100%;border:1px solid #d1d5db;border-radius:4px;padding:.6rem .75rem;font-size:.95rem;color:#111;box-sizing:border-box;">
                </div>

                <button type="submit"
                    style="width:100%;padding:.75rem 1.5rem;background:#111;color:#fff;border:none;border-radius:4px;cursor:pointer;font-family:'Montserrat',sans-serif;font-size:.9rem;letter-spacing:.04em;font-weight:500;">
                    Pagar con Stripe
                </button>
            </form>
        </div>

        {{-- Order summary --}}
        <div style="width:280px;min-width:240px;">
            <h2 style="font-family:'Montserrat',sans-serif;font-size:.9rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;margin-bottom:1.25rem;">Resumen del pedido</h2>

            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:1.25rem 1.5rem;">
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

            <a href="{{ route('shop.cart') }}"
                style="display:block;text-align:center;margin-top:1rem;font-size:.85rem;color:#6b7280;text-decoration:underline;">
                ← Volver al carrito
            </a>
        </div>

    </div>

</div>
@endsection

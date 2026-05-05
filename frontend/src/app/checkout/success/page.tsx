'use client';

import { useEffect, useState, Suspense } from 'react';
import { useSearchParams } from 'next/navigation';
import Link from 'next/link';
import { useCart } from '@/lib/cart';
import { formatPrice } from '@/components/shop/AddToCartButton';

interface OrderView {
  order_number: string;
  total: number;
  currency: string;
  payment_status: string;
  date_created: string;
  items: Array<{
    product_name: string;
    quantity: number;
    unit_price: number;
    subtotal: number;
  }>;
}

function SuccessInner() {
  const params = useSearchParams();
  const sessionId = params.get('session_id');
  const { clear } = useCart();
  const [order, setOrder] = useState<OrderView | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!sessionId) {
      setError('Falta el identificador de sesión.');
      setLoading(false);
      return;
    }
    clear();
    fetch(`/api/shop/order-by-session?id=${encodeURIComponent(sessionId)}`)
      .then(async (r) => {
        if (!r.ok) throw new Error('Pedido no encontrado');
        return r.json();
      })
      .then((data) => {
        setOrder(data);
        setLoading(false);
      })
      .catch((e) => {
        setError(e.message);
        setLoading(false);
      });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [sessionId]);

  return (
    <main className="min-h-screen bg-[#fdfbf6] text-black">
      <div className="max-w-2xl mx-auto px-6 pt-24 pb-24 text-center">
        <p className="text-xs tracking-[0.3em] uppercase text-black/50 mb-4">Gracias</p>
        <h1 className="font-serif text-4xl md:text-5xl font-light mb-6">Pedido recibido</h1>

        {loading && <p className="text-black/60">Cargando pedido…</p>}
        {error && <p className="text-red-700">{error}</p>}

        {order && (
          <div className="mt-8">
            <p className="text-lg mb-2">
              Pedido <span className="font-medium">#{order.order_number}</span>
            </p>
            <p className="text-black/60 mb-8 text-sm">
              Te hemos enviado un email de confirmación. Estado: {order.payment_status}
            </p>

            <div className="bg-white p-6 text-left">
              <ul className="divide-y divide-black/10">
                {order.items?.map((it, i) => (
                  <li key={i} className="py-3 flex justify-between text-sm">
                    <span>
                      {it.product_name} × {it.quantity}
                    </span>
                    <span>{formatPrice(it.subtotal, order.currency)}</span>
                  </li>
                ))}
              </ul>
              <div className="flex justify-between mt-4 pt-4 border-t border-black/10 font-medium">
                <span>Total</span>
                <span>{formatPrice(order.total, order.currency)}</span>
              </div>
            </div>
          </div>
        )}

        <Link
          href="/shop"
          className="inline-block mt-12 bg-black text-white px-8 py-3 text-xs tracking-[0.25em] uppercase hover:bg-black/80"
        >
          Seguir comprando
        </Link>
      </div>
    </main>
  );
}

export default function SuccessPage() {
  return (
    <Suspense fallback={<div className="min-h-screen bg-[#fdfbf6]" />}>
      <SuccessInner />
    </Suspense>
  );
}

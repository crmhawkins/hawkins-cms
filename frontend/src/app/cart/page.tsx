'use client';

import Link from 'next/link';
import { useEffect, useState } from 'react';
import { useCart } from '@/lib/cart';
import { formatPrice } from '@/components/shop/AddToCartButton';

interface ShopConfig {
  currency: string;
  flatShipping: number;
  taxRate: number;
}

export default function CartPage() {
  const { items, subtotal, update, remove, clear } = useCart();
  const [config, setConfig] = useState<ShopConfig>({
    currency: 'EUR',
    flatShipping: 0,
    taxRate: 21,
  });
  const [email, setEmail] = useState('');
  const [name, setName] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetch('/api/shop/config')
      .then((r) => r.json())
      .then((c) => setConfig(c))
      .catch(() => {});
  }, []);

  const shipping = items.length > 0 ? config.flatShipping : 0;
  const taxableBase = subtotal + shipping;
  const tax = (taxableBase * config.taxRate) / 100;
  const total = taxableBase + tax;

  const checkout = async () => {
    if (!email) {
      setError('Por favor, introduce tu email.');
      return;
    }
    setLoading(true);
    setError(null);
    try {
      const res = await fetch('/api/shop/checkout', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          items: items.map((i) => ({ id: i.id, quantity: i.quantity })),
          customer_email: email,
          customer_name: name || undefined,
        }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'Error al procesar el pago');
      if (data.url) {
        window.location.href = data.url;
      } else {
        throw new Error('No se recibió URL de pago');
      }
    } catch (e: any) {
      setError(e.message);
      setLoading(false);
    }
  };

  return (
    <main className="min-h-screen bg-[#fdfbf6] text-black">
      <div className="max-w-5xl mx-auto px-6 pt-16 pb-24">
        <h1 className="font-serif text-4xl md:text-5xl font-light mb-12">Tu carrito</h1>

        {items.length === 0 ? (
          <div className="text-center py-16">
            <p className="text-black/60 mb-6">Tu carrito está vacío.</p>
            <Link
              href="/shop"
              className="inline-block bg-black text-white px-8 py-3 text-xs tracking-[0.25em] uppercase hover:bg-black/80"
            >
              Ir a la tienda
            </Link>
          </div>
        ) : (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div className="lg:col-span-2">
              <ul className="divide-y divide-black/10 border-t border-b border-black/10">
                {items.map((item) => (
                  <li key={item.id} className="py-6 flex gap-4">
                    <div className="w-24 h-24 bg-white flex-shrink-0 overflow-hidden">
                      {item.image && (
                        // eslint-disable-next-line @next/next/no-img-element
                        <img
                          src={`/assets/${item.image}`}
                          alt={item.name}
                          className="w-full h-full object-cover"
                        />
                      )}
                    </div>
                    <div className="flex-1 flex flex-col">
                      <div className="flex justify-between">
                        <Link href={`/shop/${item.slug}`} className="font-serif text-lg hover:underline">
                          {item.name}
                        </Link>
                        <button
                          type="button"
                          onClick={() => remove(item.id)}
                          className="text-xs tracking-widest uppercase text-black/50 hover:text-black"
                        >
                          Eliminar
                        </button>
                      </div>
                      <p className="text-sm text-black/60 mb-3">
                        {formatPrice(item.price, config.currency)}
                      </p>
                      <div className="mt-auto flex items-center gap-3">
                        <div className="inline-flex items-center border border-black/20">
                          <button
                            type="button"
                            onClick={() => update(item.id, item.quantity - 1)}
                            className="px-3 py-1 hover:bg-black/5"
                          >
                            −
                          </button>
                          <span className="px-3 py-1 min-w-[2.5rem] text-center">{item.quantity}</span>
                          <button
                            type="button"
                            onClick={() => update(item.id, item.quantity + 1)}
                            className="px-3 py-1 hover:bg-black/5"
                          >
                            +
                          </button>
                        </div>
                        <span className="ml-auto">
                          {formatPrice(item.price * item.quantity, config.currency)}
                        </span>
                      </div>
                    </div>
                  </li>
                ))}
              </ul>
              <button
                type="button"
                onClick={clear}
                className="mt-6 text-xs tracking-widest uppercase text-black/50 hover:text-black"
              >
                Vaciar carrito
              </button>
            </div>

            <aside className="bg-white p-6 h-fit">
              <h2 className="font-serif text-xl mb-6">Resumen</h2>
              <dl className="space-y-3 text-sm">
                <div className="flex justify-between">
                  <dt className="text-black/60">Subtotal</dt>
                  <dd>{formatPrice(subtotal, config.currency)}</dd>
                </div>
                <div className="flex justify-between">
                  <dt className="text-black/60">Envío</dt>
                  <dd>
                    {shipping === 0 ? 'Gratis' : formatPrice(shipping, config.currency)}
                  </dd>
                </div>
                <div className="flex justify-between">
                  <dt className="text-black/60">IVA ({config.taxRate}%)</dt>
                  <dd>{formatPrice(tax, config.currency)}</dd>
                </div>
                <div className="flex justify-between pt-3 border-t border-black/10 text-base font-medium">
                  <dt>Total</dt>
                  <dd>{formatPrice(total, config.currency)}</dd>
                </div>
              </dl>

              <div className="mt-6 space-y-3">
                <input
                  type="email"
                  required
                  placeholder="Email*"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="w-full border border-black/20 px-3 py-2 text-sm bg-white focus:outline-none focus:border-black"
                />
                <input
                  type="text"
                  placeholder="Nombre"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  className="w-full border border-black/20 px-3 py-2 text-sm bg-white focus:outline-none focus:border-black"
                />
              </div>

              {error && <p className="mt-3 text-sm text-red-700">{error}</p>}

              <button
                type="button"
                onClick={checkout}
                disabled={loading}
                className="mt-6 w-full bg-black text-white px-6 py-3 text-xs tracking-[0.25em] uppercase hover:bg-black/80 transition disabled:opacity-50"
              >
                {loading ? 'Procesando…' : 'Ir a pagar'}
              </button>
            </aside>
          </div>
        )}
      </div>
    </main>
  );
}

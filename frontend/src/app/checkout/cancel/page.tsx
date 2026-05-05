import Link from 'next/link';

export const metadata = { title: 'Pago cancelado' };

export default function CancelPage() {
  return (
    <main className="min-h-screen bg-[#fdfbf6] text-black">
      <div className="max-w-2xl mx-auto px-6 pt-24 pb-24 text-center">
        <p className="text-xs tracking-[0.3em] uppercase text-black/50 mb-4">Pago cancelado</p>
        <h1 className="font-serif text-4xl md:text-5xl font-light mb-6">
          No se ha completado el pago
        </h1>
        <p className="text-black/60 mb-8">
          Tu carrito sigue disponible por si quieres intentarlo de nuevo.
        </p>
        <div className="flex gap-4 justify-center">
          <Link
            href="/cart"
            className="inline-block bg-black text-white px-8 py-3 text-xs tracking-[0.25em] uppercase hover:bg-black/80"
          >
            Volver al carrito
          </Link>
          <Link
            href="/shop"
            className="inline-block border border-black px-8 py-3 text-xs tracking-[0.25em] uppercase hover:bg-black hover:text-white"
          >
            Seguir comprando
          </Link>
        </div>
      </div>
    </main>
  );
}

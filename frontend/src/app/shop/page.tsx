import Link from 'next/link';
import { requireModule } from '@/lib/modules';
import { shop } from '@/lib/shop';
import { AddToCartButton, formatPrice } from '@/components/shop/AddToCartButton';

export const metadata = {
  title: 'Tienda',
};

export default async function ShopPage({
  searchParams,
}: {
  searchParams: Promise<{ featured?: string }>;
}) {
  await requireModule('ecommerce');
  const params = await searchParams;
  const featured = params.featured === '1';
  const products = await shop.listProducts(featured, 60);

  return (
    <main className="min-h-screen bg-[#fdfbf6] text-black">
      <header className="max-w-6xl mx-auto px-6 pt-24 pb-12">
        <p className="text-xs tracking-[0.3em] uppercase text-black/50 mb-4">Tienda</p>
        <h1 className="font-serif text-5xl md:text-6xl font-light">Nuestros productos</h1>
        <div className="mt-8 flex gap-6 text-xs tracking-[0.2em] uppercase">
          <Link
            href="/shop"
            className={!featured ? 'underline underline-offset-4' : 'text-black/50 hover:text-black'}
          >
            Todos
          </Link>
          <Link
            href="/shop?featured=1"
            className={featured ? 'underline underline-offset-4' : 'text-black/50 hover:text-black'}
          >
            Destacados
          </Link>
        </div>
      </header>

      <section className="max-w-6xl mx-auto px-6 pb-24">
        {products.length === 0 ? (
          <p className="text-black/60 py-16 text-center">No hay productos disponibles.</p>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            {products.map((p) => {
              const img = p.cover_image
                ? `/assets/${p.cover_image}`
                : 'https://via.placeholder.com/600x600?text=%20';
              return (
                <article key={p.id} className="group">
                  <Link href={`/shop/${p.slug}`} className="block overflow-hidden bg-white aspect-square mb-4">
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img
                      src={img}
                      alt={p.name}
                      className="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                    />
                  </Link>
                  <div className="flex items-start justify-between gap-4 mb-3">
                    <div>
                      <h2 className="font-serif text-xl font-light leading-tight">
                        <Link href={`/shop/${p.slug}`} className="hover:underline underline-offset-4">
                          {p.name}
                        </Link>
                      </h2>
                      {p.short_description && (
                        <p className="text-sm text-black/60 mt-1 line-clamp-2">{p.short_description}</p>
                      )}
                    </div>
                    <div className="text-right whitespace-nowrap">
                      <p className="text-base">{formatPrice(p.price, p.currency)}</p>
                      {p.compare_price && p.compare_price > p.price && (
                        <p className="text-xs line-through text-black/40">
                          {formatPrice(p.compare_price, p.currency)}
                        </p>
                      )}
                    </div>
                  </div>
                  <AddToCartButton
                    product={p}
                    className="w-full bg-black text-white px-4 py-3 text-[10px] tracking-[0.25em] uppercase hover:bg-black/80 transition"
                  />
                </article>
              );
            })}
          </div>
        )}
      </section>
    </main>
  );
}

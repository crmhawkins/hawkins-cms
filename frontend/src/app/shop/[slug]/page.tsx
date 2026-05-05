import { notFound } from 'next/navigation';
import Link from 'next/link';
import type { Metadata } from 'next';
import { requireModule } from '@/lib/modules';
import { shop } from '@/lib/shop';
import { AddToCartButton, formatPrice } from '@/components/shop/AddToCartButton';

export async function generateMetadata({
  params,
}: {
  params: Promise<{ slug: string }>;
}): Promise<Metadata> {
  const { slug } = await params;
  const p = await shop.getProduct(slug);
  if (!p) return { title: 'Producto no encontrado' };
  return {
    title: p.name,
    description: p.short_description || undefined,
  };
}

export default async function ProductDetailPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  await requireModule('ecommerce');
  const { slug } = await params;
  const product = await shop.getProduct(slug);
  if (!product) notFound();

  const cover = product.cover_image
    ? `/assets/${product.cover_image}`
    : 'https://via.placeholder.com/900x900?text=%20';
  const gallery = (product.gallery || []).map((g) => `/assets/${g}`);

  return (
    <main className="min-h-screen bg-[#fdfbf6] text-black">
      <div className="max-w-6xl mx-auto px-6 pt-16 pb-24">
        <nav className="text-xs tracking-[0.2em] uppercase mb-8">
          <Link href="/shop" className="text-black/50 hover:text-black">
            ← Tienda
          </Link>
        </nav>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-12">
          {/* Galería */}
          <div>
            <div className="aspect-square bg-white overflow-hidden mb-4">
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src={cover} alt={product.name} className="w-full h-full object-cover" />
            </div>
            {gallery.length > 0 && (
              <div className="grid grid-cols-4 gap-2">
                {gallery.map((g, i) => (
                  // eslint-disable-next-line @next/next/no-img-element
                  <img
                    key={i}
                    src={g}
                    alt={`${product.name} ${i + 1}`}
                    className="aspect-square object-cover bg-white"
                  />
                ))}
              </div>
            )}
          </div>

          {/* Info */}
          <div>
            {product.sku && (
              <p className="text-xs tracking-[0.3em] uppercase text-black/50 mb-3">
                SKU {product.sku}
              </p>
            )}
            <h1 className="font-serif text-4xl md:text-5xl font-light mb-4">{product.name}</h1>

            <div className="flex items-baseline gap-3 mb-6">
              <span className="text-2xl">{formatPrice(product.price, product.currency)}</span>
              {product.compare_price && product.compare_price > product.price && (
                <span className="text-base line-through text-black/40">
                  {formatPrice(product.compare_price, product.currency)}
                </span>
              )}
            </div>

            {product.short_description && (
              <p className="text-black/70 leading-relaxed mb-8">{product.short_description}</p>
            )}

            {product.track_stock && product.stock <= 0 ? (
              <p className="text-sm text-red-700 mb-6">Agotado</p>
            ) : (
              <div className="mb-8">
                <AddToCartButton product={product} showQuantity label="Añadir al carrito" />
              </div>
            )}

            {product.track_stock && product.stock > 0 && product.stock < 10 && (
              <p className="text-xs text-black/50">Solo quedan {product.stock} unidades</p>
            )}
          </div>
        </div>

        {product.description && (
          <div className="max-w-3xl mx-auto mt-24 pt-16 border-t border-black/10">
            <h2 className="font-serif text-2xl font-light mb-6">Descripción</h2>
            <div
              className="prose prose-neutral max-w-none text-black/80 leading-relaxed"
              dangerouslySetInnerHTML={{ __html: product.description }}
            />
          </div>
        )}
      </div>
    </main>
  );
}

'use client';

import type { ComponentConfig } from '@measured/puck';
import { useEffect, useState } from 'react';
import { useCart } from '@/lib/cart';

export type ProductGridProps = {
  heading?: string;
  subtitle?: string;
  source: 'featured' | 'all' | 'manual';
  manualProducts?: string;
  columns: 2 | 3 | 4;
  limit: number;
};

interface APIProduct {
  id: string;
  name: string;
  slug: string;
  price: number;
  compare_price?: number;
  currency: string;
  short_description?: string;
  cover_image?: string;
}

const colMap: Record<number, string> = {
  2: 'sm:grid-cols-2',
  3: 'sm:grid-cols-2 lg:grid-cols-3',
  4: 'sm:grid-cols-2 lg:grid-cols-4',
};

function fmt(v: number, c = 'EUR') {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: c }).format(v);
}

const ProductGridRender = ({
  heading,
  subtitle,
  source,
  manualProducts,
  columns,
  limit,
}: ProductGridProps) => {
  const [products, setProducts] = useState<APIProduct[]>([]);
  const [loading, setLoading] = useState(true);
  const { add } = useCart();

  useEffect(() => {
    const q = new URLSearchParams({
      source,
      limit: String(limit || 12),
    });
    if (source === 'manual' && manualProducts) q.set('slugs', manualProducts);
    fetch(`/api/shop/products?${q}`)
      .then((r) => r.json())
      .then((j) => {
        setProducts(j.data || []);
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, [source, manualProducts, limit]);

  return (
    <section className="w-full py-16 px-6 bg-[#fdfbf6]">
      <div className="max-w-6xl mx-auto">
        {(heading || subtitle) && (
          <header className="text-center mb-12">
            {heading && (
              <h2 className="font-serif text-3xl md:text-4xl font-light text-black">{heading}</h2>
            )}
            {subtitle && <p className="mt-3 text-black/60">{subtitle}</p>}
          </header>
        )}

        {loading ? (
          <p className="text-center text-black/50">Cargando…</p>
        ) : products.length === 0 ? (
          <p className="text-center text-black/50">No hay productos.</p>
        ) : (
          <div className={`grid grid-cols-1 ${colMap[columns] || colMap[3]} gap-8`}>
            {products.map((p) => {
              const img = p.cover_image
                ? `/assets/${p.cover_image}`
                : 'https://via.placeholder.com/600x600?text=%20';
              return (
                <article key={p.id} className="group">
                  <a href={`/shop/${p.slug}`} className="block overflow-hidden bg-white aspect-square mb-3">
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img
                      src={img}
                      alt={p.name}
                      className="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                    />
                  </a>
                  <div className="flex justify-between items-start gap-3">
                    <a href={`/shop/${p.slug}`} className="font-serif text-lg hover:underline">
                      {p.name}
                    </a>
                    <span className="text-sm whitespace-nowrap">{fmt(p.price, p.currency)}</span>
                  </div>
                  <button
                    type="button"
                    onClick={() =>
                      add({
                        id: p.id,
                        name: p.name,
                        slug: p.slug,
                        price: p.price,
                        image: p.cover_image,
                      })
                    }
                    className="mt-3 w-full bg-black text-white px-4 py-2 text-[10px] tracking-[0.25em] uppercase hover:bg-black/80 transition"
                  >
                    Añadir al carrito
                  </button>
                </article>
              );
            })}
          </div>
        )}
      </div>
    </section>
  );
};

export const ProductGrid: { config: ComponentConfig<ProductGridProps> } = {
  config: {
    label: 'Grid de productos',
    fields: {
      heading: { type: 'text', label: 'Título' },
      subtitle: { type: 'text', label: 'Subtítulo' },
      source: {
        type: 'select',
        label: 'Origen',
        options: [
          { label: 'Destacados', value: 'featured' },
          { label: 'Todos', value: 'all' },
          { label: 'Manual (slugs)', value: 'manual' },
        ],
      },
      manualProducts: {
        type: 'text',
        label: 'Slugs separados por coma (solo si Manual)',
      },
      columns: {
        type: 'select',
        label: 'Columnas',
        options: [
          { label: '2', value: 2 },
          { label: '3', value: 3 },
          { label: '4', value: 4 },
        ],
      },
      limit: { type: 'number', label: 'Máximo a mostrar', min: 1, max: 48 },
    },
    defaultProps: {
      heading: 'Nuestros productos',
      subtitle: '',
      source: 'featured',
      manualProducts: '',
      columns: 3,
      limit: 9,
    },
    render: ProductGridRender,
  },
};

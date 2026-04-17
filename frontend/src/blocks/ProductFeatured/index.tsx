'use client';

import type { ComponentConfig } from '@measured/puck';
import { useEffect, useState } from 'react';
import { useCart } from '@/lib/cart';

export type ProductFeaturedProps = {
  productSlug: string;
  layout: 'image-left' | 'image-right' | 'overlay';
  showPrice: boolean;
  ctaLabel: string;
};

interface APIProduct {
  id: string;
  name: string;
  slug: string;
  price: number;
  currency: string;
  short_description?: string;
  cover_image?: string;
}

function fmt(v: number, c = 'EUR') {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: c }).format(v);
}

const ProductFeaturedRender = ({
  productSlug,
  layout,
  showPrice,
  ctaLabel,
}: ProductFeaturedProps) => {
  const [product, setProduct] = useState<APIProduct | null>(null);
  const [loading, setLoading] = useState(true);
  const { add } = useCart();

  useEffect(() => {
    if (!productSlug) {
      setLoading(false);
      return;
    }
    fetch(`/api/shop/product/${encodeURIComponent(productSlug)}`)
      .then((r) => (r.ok ? r.json() : null))
      .then((p) => {
        setProduct(p);
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, [productSlug]);

  if (loading) {
    return <section className="py-16 px-6 text-center text-black/50">Cargando…</section>;
  }
  if (!product) {
    return (
      <section className="py-16 px-6 text-center text-black/50">
        Selecciona un producto (slug) para este bloque.
      </section>
    );
  }

  const img = product.cover_image
    ? `/assets/${product.cover_image}`
    : 'https://via.placeholder.com/1200x1200?text=%20';

  const handleAdd = () =>
    add({
      id: product.id,
      name: product.name,
      slug: product.slug,
      price: product.price,
      image: product.cover_image,
    });

  if (layout === 'overlay') {
    return (
      <section className="relative w-full min-h-[70vh] flex items-center justify-center overflow-hidden">
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img src={img} alt={product.name} className="absolute inset-0 w-full h-full object-cover" />
        <div className="absolute inset-0 bg-black/40" />
        <div className="relative z-10 text-center text-white max-w-xl px-6">
          <h2 className="font-serif text-4xl md:text-5xl font-light mb-4">{product.name}</h2>
          {product.short_description && (
            <p className="text-white/80 mb-6">{product.short_description}</p>
          )}
          {showPrice && <p className="text-xl mb-6">{fmt(product.price, product.currency)}</p>}
          <button
            type="button"
            onClick={handleAdd}
            className="bg-white text-black px-8 py-3 text-xs tracking-[0.25em] uppercase hover:bg-white/90"
          >
            {ctaLabel}
          </button>
        </div>
      </section>
    );
  }

  const reverse = layout === 'image-right';
  return (
    <section className="w-full py-16 px-6 bg-[#fdfbf6]">
      <div
        className={`max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center ${
          reverse ? 'md:[&>*:first-child]:order-2' : ''
        }`}
      >
        <div className="aspect-square bg-white overflow-hidden">
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img src={img} alt={product.name} className="w-full h-full object-cover" />
        </div>
        <div>
          <h2 className="font-serif text-3xl md:text-4xl font-light mb-4 text-black">
            {product.name}
          </h2>
          {product.short_description && (
            <p className="text-black/70 mb-6 leading-relaxed">{product.short_description}</p>
          )}
          {showPrice && (
            <p className="text-xl mb-6 text-black">{fmt(product.price, product.currency)}</p>
          )}
          <div className="flex gap-3">
            <button
              type="button"
              onClick={handleAdd}
              className="bg-black text-white px-8 py-3 text-xs tracking-[0.25em] uppercase hover:bg-black/80"
            >
              {ctaLabel}
            </button>
            <a
              href={`/shop/${product.slug}`}
              className="border border-black px-8 py-3 text-xs tracking-[0.25em] uppercase hover:bg-black hover:text-white"
            >
              Ver detalle
            </a>
          </div>
        </div>
      </div>
    </section>
  );
};

export const ProductFeatured: { config: ComponentConfig<ProductFeaturedProps> } = {
  config: {
    label: 'Producto destacado',
    fields: {
      productSlug: { type: 'text', label: 'Slug del producto' },
      layout: {
        type: 'select',
        label: 'Layout',
        options: [
          { label: 'Imagen izquierda', value: 'image-left' },
          { label: 'Imagen derecha', value: 'image-right' },
          { label: 'Overlay', value: 'overlay' },
        ],
      },
      showPrice: {
        type: 'radio',
        label: 'Mostrar precio',
        options: [
          { label: 'Sí', value: true },
          { label: 'No', value: false },
        ],
      },
      ctaLabel: { type: 'text', label: 'Texto del botón' },
    },
    defaultProps: {
      productSlug: '',
      layout: 'image-left',
      showPrice: true,
      ctaLabel: 'Añadir al carrito',
    },
    render: ProductFeaturedRender,
  },
};

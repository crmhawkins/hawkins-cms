'use client';

import { useState } from 'react';
import { useCart } from '@/lib/cart';
import type { Product } from '@/lib/shop';

interface Props {
  product: Pick<Product, 'id' | 'name' | 'slug' | 'price' | 'cover_image'>;
  quantity?: number;
  label?: string;
  className?: string;
  showQuantity?: boolean;
}

export function AddToCartButton({
  product,
  quantity: initialQty = 1,
  label = 'Añadir al carrito',
  className,
  showQuantity = false,
}: Props) {
  const { add } = useCart();
  const [qty, setQty] = useState(initialQty);

  const handleAdd = () => {
    add(
      {
        id: product.id,
        name: product.name,
        slug: product.slug,
        price: product.price,
        image: product.cover_image,
      },
      qty
    );
  };

  return (
    <div className="flex items-center gap-3">
      {showQuantity && (
        <div className="flex items-center border border-black/20">
          <button
            type="button"
            onClick={() => setQty((q) => Math.max(1, q - 1))}
            className="px-3 py-2 text-sm hover:bg-black/5"
            aria-label="Disminuir cantidad"
          >
            −
          </button>
          <span className="px-3 py-2 text-sm min-w-[2.5rem] text-center">{qty}</span>
          <button
            type="button"
            onClick={() => setQty((q) => q + 1)}
            className="px-3 py-2 text-sm hover:bg-black/5"
            aria-label="Aumentar cantidad"
          >
            +
          </button>
        </div>
      )}
      <button
        type="button"
        onClick={handleAdd}
        className={
          className ||
          'bg-black text-white px-6 py-3 text-xs tracking-[0.25em] uppercase hover:bg-black/80 transition'
        }
      >
        {label}
      </button>
    </div>
  );
}

export function formatPrice(amount: number, currency = 'EUR') {
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency,
  }).format(amount);
}

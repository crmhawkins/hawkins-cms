'use client';

import type { ComponentConfig } from '@measured/puck';
import { useState } from 'react';
import { useCart } from '@/lib/cart';

export type CartMiniProps = {
  label: string;
  align: 'left' | 'right';
};

function fmt(v: number, c = 'EUR') {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: c }).format(v);
}

const CartMiniRender = ({ label, align }: CartMiniProps) => {
  const { items, count, subtotal, remove } = useCart();
  const [open, setOpen] = useState(false);

  return (
    <div className={`relative inline-block ${align === 'right' ? 'float-right' : ''}`}>
      <button
        type="button"
        onClick={() => setOpen((v) => !v)}
        className="relative flex items-center gap-2 px-4 py-2 text-xs tracking-[0.25em] uppercase hover:bg-black/5"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
          <path d="M6 7h12l-1.5 11H7.5L6 7z" />
          <path d="M9 7V5a3 3 0 016 0v2" />
        </svg>
        <span>{label}</span>
        {count > 0 && (
          <span className="absolute -top-1 -right-1 bg-black text-white text-[10px] rounded-full h-5 w-5 flex items-center justify-center">
            {count}
          </span>
        )}
      </button>

      {open && (
        <div
          className={`absolute mt-2 w-80 bg-white border border-black/10 shadow-lg z-50 ${
            align === 'right' ? 'right-0' : 'left-0'
          }`}
        >
          <div className="p-4">
            {items.length === 0 ? (
              <p className="text-sm text-black/60 py-4 text-center">Carrito vacío</p>
            ) : (
              <>
                <ul className="divide-y divide-black/10 max-h-64 overflow-y-auto">
                  {items.map((it) => (
                    <li key={it.id} className="py-3 flex gap-3 text-sm">
                      <div className="flex-1">
                        <p className="font-medium">{it.name}</p>
                        <p className="text-black/60 text-xs">
                          {it.quantity} × {fmt(it.price)}
                        </p>
                      </div>
                      <button
                        type="button"
                        onClick={() => remove(it.id)}
                        className="text-black/40 hover:text-black text-xs"
                      >
                        ×
                      </button>
                    </li>
                  ))}
                </ul>
                <div className="flex justify-between mt-4 pt-3 border-t border-black/10 text-sm">
                  <span>Subtotal</span>
                  <span>{fmt(subtotal)}</span>
                </div>
                <div className="mt-4 flex gap-2">
                  <a
                    href="/cart"
                    className="flex-1 text-center border border-black px-3 py-2 text-[10px] tracking-[0.25em] uppercase hover:bg-black hover:text-white"
                  >
                    Ver carrito
                  </a>
                  <a
                    href="/cart"
                    className="flex-1 text-center bg-black text-white px-3 py-2 text-[10px] tracking-[0.25em] uppercase hover:bg-black/80"
                  >
                    Pagar
                  </a>
                </div>
              </>
            )}
          </div>
        </div>
      )}
    </div>
  );
};

export const CartMini: { config: ComponentConfig<CartMiniProps> } = {
  config: {
    label: 'Carrito (mini)',
    fields: {
      label: { type: 'text', label: 'Etiqueta' },
      align: {
        type: 'radio',
        label: 'Alineación del popover',
        options: [
          { label: 'Izquierda', value: 'left' },
          { label: 'Derecha', value: 'right' },
        ],
      },
    },
    defaultProps: {
      label: 'Carrito',
      align: 'right',
    },
    render: CartMiniRender,
  },
};

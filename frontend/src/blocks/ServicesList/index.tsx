'use client';

import { useEffect, useState } from 'react';
import type { ComponentConfig } from '@measured/puck';

export type ServicesListProps = {
  heading: string;
  subtitle?: string;
  columns: 2 | 3 | 4;
  source: 'all' | 'featured' | 'manual';
  manualIds: string;
  ctaLabel: string;
};

interface Service {
  id: string;
  name: string;
  description?: string;
  duration_minutes: number;
  price?: number;
  image_url?: string | null;
}

const colsMap = {
  2: 'md:grid-cols-2',
  3: 'md:grid-cols-3',
  4: 'md:grid-cols-4',
} as const;

const ServicesListRender = ({
  heading,
  subtitle,
  columns,
  source,
  manualIds,
  ctaLabel,
}: ServicesListProps) => {
  const [services, setServices] = useState<Service[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch('/api/booking/services')
      .then((r) => r.json())
      .then((d) => setServices(Array.isArray(d.services) ? d.services : []))
      .catch(() => setServices([]))
      .finally(() => setLoading(false));
  }, []);

  let list = services;
  if (source === 'manual' && manualIds) {
    const ids = manualIds.split(',').map((s) => s.trim()).filter(Boolean);
    list = services.filter((s) => ids.includes(s.id));
  }
  // 'featured' aún no tiene flag en backend — se trata como 'all'.

  return (
    <section className="w-full py-20 bg-[#fdfcf7] text-black">
      <div className="max-w-6xl mx-auto px-6">
        <header className="text-center mb-12">
          <h2 className="font-serif text-3xl md:text-4xl font-light mb-3">{heading}</h2>
          {subtitle && <p className="text-black/70 max-w-xl mx-auto">{subtitle}</p>}
        </header>

        {loading ? (
          <p className="text-center text-sm text-black/50">Cargando…</p>
        ) : list.length === 0 ? (
          <p className="text-center text-sm text-black/60">No hay servicios disponibles.</p>
        ) : (
          <div className={`grid grid-cols-1 ${colsMap[columns]} gap-6`}>
            {list.map((s) => (
              <article key={s.id} className="border border-black/10 bg-white flex flex-col">
                {s.image_url && (
                  <div
                    className="aspect-[4/3] bg-black/5 bg-cover bg-center"
                    style={{ backgroundImage: `url(${s.image_url})` }}
                  />
                )}
                <div className="p-6 flex flex-col flex-1">
                  <h3 className="font-serif text-xl mb-2">{s.name}</h3>
                  {s.description && (
                    <p className="text-sm text-black/60 mb-4 leading-relaxed flex-1">
                      {s.description}
                    </p>
                  )}
                  <div className="flex items-center justify-between text-xs tracking-wider text-black/60 mb-5">
                    <span>{s.duration_minutes} min</span>
                    {typeof s.price === 'number' && <span>{s.price} €</span>}
                  </div>
                  <a
                    href={`/reservar?service=${encodeURIComponent(s.id)}`}
                    className="inline-block text-center px-6 py-3 text-xs tracking-[0.25em] uppercase bg-black text-white hover:bg-black/90 transition"
                  >
                    {ctaLabel || 'Reservar'}
                  </a>
                </div>
              </article>
            ))}
          </div>
        )}
      </div>
    </section>
  );
};

export const ServicesList: { config: ComponentConfig<ServicesListProps> } = {
  config: {
    label: 'Lista de servicios',
    fields: {
      heading: { type: 'text', label: 'Encabezado' },
      subtitle: { type: 'textarea', label: 'Subtítulo' },
      columns: {
        type: 'select',
        label: 'Columnas',
        options: [
          { label: '2', value: 2 },
          { label: '3', value: 3 },
          { label: '4', value: 4 },
        ],
      },
      source: {
        type: 'select',
        label: 'Origen',
        options: [
          { label: 'Todos', value: 'all' },
          { label: 'Destacados', value: 'featured' },
          { label: 'Manual (IDs)', value: 'manual' },
        ],
      },
      manualIds: { type: 'text', label: 'IDs separados por coma (si manual)' },
      ctaLabel: { type: 'text', label: 'Texto del botón' },
    },
    defaultProps: {
      heading: 'Nuestros servicios',
      subtitle: 'Elige el servicio que mejor se adapta a ti y reserva en un minuto.',
      columns: 3,
      source: 'all',
      manualIds: '',
      ctaLabel: 'Reservar',
    },
    render: ServicesListRender,
  },
};

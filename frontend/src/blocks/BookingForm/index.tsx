'use client';

import { useEffect, useState } from 'react';
import type { ComponentConfig } from '@measured/puck';
import { BookingForm as BookingFormUI, type BookingFormService } from '@/components/BookingForm';

export type BookingFormBlockProps = {
  heading: string;
  subtitle?: string;
  defaultServiceId?: string;
  showInfo: boolean;
  variant: 'embedded' | 'linked';
  ctaLabel: string;
  ctaUrl: string;
};

const BookingFormRender = ({
  heading,
  subtitle,
  defaultServiceId,
  showInfo,
  variant,
  ctaLabel,
  ctaUrl,
}: BookingFormBlockProps) => {
  const [services, setServices] = useState<BookingFormService[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (variant !== 'embedded') {
      setLoading(false);
      return;
    }
    fetch('/api/booking/services')
      .then((r) => r.json())
      .then((d) => setServices(Array.isArray(d.services) ? d.services : []))
      .catch(() => setServices([]))
      .finally(() => setLoading(false));
  }, [variant]);

  if (variant === 'linked') {
    return (
      <section className="w-full py-20 bg-[#fdfcf7] text-black">
        <div className="max-w-3xl mx-auto px-6 text-center">
          <h2 className="font-serif text-3xl md:text-4xl font-light mb-4">{heading}</h2>
          {subtitle && (
            <p className="text-black/70 mb-8 max-w-xl mx-auto">{subtitle}</p>
          )}
          <a
            href={ctaUrl || '/reservar'}
            className="inline-block px-10 py-4 text-xs tracking-[0.25em] uppercase bg-black text-white hover:bg-black/90 transition"
          >
            {ctaLabel || 'Reservar cita'}
          </a>
        </div>
      </section>
    );
  }

  return (
    <section className="w-full py-20 bg-[#fdfcf7] text-black">
      <div className="max-w-3xl mx-auto px-6">
        <header className="text-center mb-12">
          <h2 className="font-serif text-3xl md:text-4xl font-light mb-3">{heading}</h2>
          {subtitle && <p className="text-black/70 max-w-xl mx-auto">{subtitle}</p>}
        </header>

        {loading ? (
          <p className="text-center text-sm text-black/50">Cargando…</p>
        ) : services.length === 0 ? (
          <p className="text-center text-sm text-black/60">
            No hay servicios disponibles en este momento.
          </p>
        ) : (
          <>
            {showInfo && services.length === 1 && services[0].description && (
              <p className="text-sm text-black/60 text-center mb-10 max-w-xl mx-auto">
                {services[0].description}
              </p>
            )}
            <BookingFormUI services={services} defaultServiceId={defaultServiceId} />
          </>
        )}
      </div>
    </section>
  );
};

export const BookingForm: { config: ComponentConfig<BookingFormBlockProps> } = {
  config: {
    label: 'Formulario de reserva',
    fields: {
      heading: { type: 'text', label: 'Encabezado' },
      subtitle: { type: 'textarea', label: 'Subtítulo' },
      variant: {
        type: 'select',
        label: 'Variante',
        options: [
          { label: 'Formulario embebido', value: 'embedded' },
          { label: 'Botón a /reservar', value: 'linked' },
        ],
      },
      defaultServiceId: { type: 'text', label: 'ID del servicio por defecto (opcional)' },
      showInfo: { type: 'radio', label: 'Mostrar descripción del servicio', options: [
        { label: 'Sí', value: true as any },
        { label: 'No', value: false as any },
      ] },
      ctaLabel: { type: 'text', label: 'Texto del botón (solo variante botón)' },
      ctaUrl: { type: 'text', label: 'URL del botón (solo variante botón)' },
    },
    defaultProps: {
      heading: 'Reserva tu cita',
      subtitle: 'Selecciona servicio, fecha y horario. Recibirás la confirmación por email.',
      defaultServiceId: '',
      showInfo: true,
      variant: 'embedded',
      ctaLabel: 'Reservar ahora',
      ctaUrl: '/reservar',
    },
    render: BookingFormRender,
  },
};

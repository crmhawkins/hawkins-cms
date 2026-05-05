import type { ComponentConfig } from '@measured/puck';

export type ImageTextProps = {
  imageUrl: string;
  imageAlt: string;
  heading: string;
  body: string;
  ctaLabel?: string;
  ctaUrl?: string;
  imageSide: 'left' | 'right';
  background: 'none' | 'light';
};

const bgMap = {
  none: 'bg-transparent',
  light: 'bg-neutral-50',
};

const ImageTextRender = ({
  imageUrl,
  imageAlt,
  heading,
  body,
  ctaLabel,
  ctaUrl,
  imageSide,
  background,
}: ImageTextProps) => {
  const imageFirst = imageSide === 'left';
  return (
    <section className={`w-full ${bgMap[background]}`}>
      <div className="max-w-6xl mx-auto px-6 py-20 md:py-28 grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-16 items-center">
        <div className={`${imageFirst ? 'md:order-1' : 'md:order-2'}`}>
          <div className="aspect-[4/5] w-full overflow-hidden bg-neutral-200">
            <img src={imageUrl} alt={imageAlt} className="w-full h-full object-cover" />
          </div>
        </div>
        <div className={`${imageFirst ? 'md:order-2' : 'md:order-1'}`}>
          <h2 className="font-serif text-3xl md:text-5xl font-light text-neutral-900 mb-6 leading-tight">
            {heading}
          </h2>
          <div
            className="font-sans text-base md:text-lg text-neutral-700 leading-relaxed space-y-4"
            dangerouslySetInnerHTML={{ __html: body }}
          />
          {ctaLabel && ctaUrl && (
            <a
              href={ctaUrl}
              className="inline-block mt-8 bg-neutral-900 text-white px-8 py-4 text-xs tracking-[0.25em] uppercase hover:bg-neutral-800 transition"
            >
              {ctaLabel}
            </a>
          )}
        </div>
      </div>
    </section>
  );
};

export const ImageText: { config: ComponentConfig<ImageTextProps> } = {
  config: {
    label: 'Imagen + Texto',
    fields: {
      imageUrl: { type: 'text', label: 'Imagen (URL)' },
      imageAlt: { type: 'text', label: 'Texto alternativo' },
      heading: { type: 'text', label: 'Título' },
      body: { type: 'textarea', label: 'Cuerpo (admite HTML simple)' },
      ctaLabel: { type: 'text', label: 'Texto del botón' },
      ctaUrl: { type: 'text', label: 'URL del botón' },
      imageSide: {
        type: 'radio',
        label: 'Posición de la imagen',
        options: [
          { label: 'Izquierda', value: 'left' },
          { label: 'Derecha', value: 'right' },
        ],
      },
      background: {
        type: 'select',
        label: 'Fondo',
        options: [
          { label: 'Ninguno', value: 'none' },
          { label: 'Claro', value: 'light' },
        ],
      },
    },
    defaultProps: {
      imageUrl: 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&q=80',
      imageAlt: 'Interior cuidado con mobiliario de autor',
      heading: 'Un lenguaje propio',
      body: '<p>Diseñamos ambientes donde cada pieza tiene una razón de ser. Tejidos naturales, maderas cálidas y una paleta serena que deja respirar al espacio.</p><p>Trabajamos con artesanos y marcas que comparten nuestra obsesión por el detalle.</p>',
      ctaLabel: 'Ver más',
      ctaUrl: '#',
      imageSide: 'left',
      background: 'none',
    },
    render: ImageTextRender,
  },
};

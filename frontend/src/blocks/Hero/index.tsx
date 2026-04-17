import type { ComponentConfig } from '@measured/puck';

export type HeroProps = {
  title: string;
  subtitle?: string;
  kicker?: string;
  backgroundImage?: string;
  align: 'left' | 'center' | 'right';
  height: 'full' | 'large' | 'medium' | 'small';
  overlay: number;
  ctaLabel?: string;
  ctaUrl?: string;
};

const heightMap = {
  full: 'min-h-screen',
  large: 'min-h-[80vh]',
  medium: 'min-h-[60vh]',
  small: 'min-h-[40vh]',
};

const alignMap = {
  left: 'text-left items-start',
  center: 'text-center items-center',
  right: 'text-right items-end',
};

const HeroRender = ({
  title,
  subtitle,
  kicker,
  backgroundImage,
  align,
  height,
  overlay,
  ctaLabel,
  ctaUrl,
}: HeroProps) => {
  return (
    <section
      className={`relative w-full flex flex-col justify-center ${heightMap[height]} ${alignMap[align]} overflow-hidden`}
      style={{
        backgroundImage: backgroundImage ? `url(${backgroundImage})` : undefined,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        backgroundColor: backgroundImage ? undefined : '#0a0a0a',
      }}
    >
      {backgroundImage && (
        <div
          className="absolute inset-0 bg-black"
          style={{ opacity: overlay / 100 }}
        />
      )}
      <div className={`relative z-10 w-full max-w-5xl mx-auto px-6 py-24 flex flex-col ${alignMap[align]}`}>
        {kicker && (
          <p className="text-xs tracking-[0.3em] uppercase text-white/60 mb-4">
            {kicker}
          </p>
        )}
        <h1 className="font-serif text-4xl md:text-6xl lg:text-7xl font-light text-white mb-6 leading-tight">
          {title}
        </h1>
        {subtitle && (
          <p className="text-base md:text-lg text-white/80 max-w-2xl mb-8 leading-relaxed">
            {subtitle}
          </p>
        )}
        {ctaLabel && ctaUrl && (
          <a
            href={ctaUrl}
            className="inline-block bg-white text-black px-8 py-4 text-xs tracking-[0.25em] uppercase hover:bg-white/90 transition"
          >
            {ctaLabel}
          </a>
        )}
      </div>
    </section>
  );
};

export const Hero: { config: ComponentConfig<HeroProps> } = {
  config: {
    label: 'Hero',
    fields: {
      kicker: { type: 'text', label: 'Kicker (pequeño texto arriba)' },
      title: { type: 'text', label: 'Título' },
      subtitle: { type: 'textarea', label: 'Subtítulo' },
      backgroundImage: { type: 'text', label: 'Imagen de fondo (URL)' },
      overlay: {
        type: 'number',
        label: 'Oscurecer imagen (0-100)',
        min: 0,
        max: 100,
      },
      align: {
        type: 'radio',
        label: 'Alineación',
        options: [
          { label: 'Izquierda', value: 'left' },
          { label: 'Centro', value: 'center' },
          { label: 'Derecha', value: 'right' },
        ],
      },
      height: {
        type: 'select',
        label: 'Altura',
        options: [
          { label: 'Pantalla completa', value: 'full' },
          { label: 'Grande', value: 'large' },
          { label: 'Mediana', value: 'medium' },
          { label: 'Pequeña', value: 'small' },
        ],
      },
      ctaLabel: { type: 'text', label: 'Texto del botón' },
      ctaUrl: { type: 'text', label: 'URL del botón' },
    },
    defaultProps: {
      title: 'Título principal',
      subtitle: 'Una descripción breve y potente que atrape al visitante.',
      kicker: '',
      backgroundImage: 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=1920&q=80',
      align: 'center',
      height: 'large',
      overlay: 50,
      ctaLabel: '',
      ctaUrl: '',
    },
    render: HeroRender,
  },
};

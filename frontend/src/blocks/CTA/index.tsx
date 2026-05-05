import type { ComponentConfig } from '@measured/puck';

export type CTAProps = {
  heading: string;
  subtitle?: string;
  ctaLabel: string;
  ctaUrl: string;
  secondaryCtaLabel?: string;
  secondaryCtaUrl?: string;
  backgroundImage?: string;
  align: 'center' | 'left';
  variant: 'dark' | 'light' | 'transparent';
};

const alignMap = {
  center: 'text-center items-center',
  left: 'text-left items-start',
};

const variantStyles = {
  dark: {
    wrapper: 'bg-neutral-950',
    heading: 'text-white',
    subtitle: 'text-white/70',
    primary: 'bg-white text-black hover:bg-white/90',
    secondary: 'text-white border-white/40 hover:border-white',
  },
  light: {
    wrapper: 'bg-neutral-50',
    heading: 'text-neutral-900',
    subtitle: 'text-neutral-600',
    primary: 'bg-neutral-900 text-white hover:bg-neutral-800',
    secondary: 'text-neutral-900 border-neutral-300 hover:border-neutral-900',
  },
  transparent: {
    wrapper: 'bg-transparent',
    heading: 'text-neutral-900',
    subtitle: 'text-neutral-600',
    primary: 'bg-neutral-900 text-white hover:bg-neutral-800',
    secondary: 'text-neutral-900 border-neutral-300 hover:border-neutral-900',
  },
};

const CTARender = ({
  heading,
  subtitle,
  ctaLabel,
  ctaUrl,
  secondaryCtaLabel,
  secondaryCtaUrl,
  backgroundImage,
  align,
  variant,
}: CTAProps) => {
  const s = variantStyles[variant];
  const hasBg = Boolean(backgroundImage);
  const effectiveHeading = hasBg ? 'text-white' : s.heading;
  const effectiveSubtitle = hasBg ? 'text-white/80' : s.subtitle;
  const effectivePrimary = hasBg ? 'bg-white text-black hover:bg-white/90' : s.primary;
  const effectiveSecondary = hasBg ? 'text-white border-white/40 hover:border-white' : s.secondary;

  return (
    <section
      className={`relative w-full overflow-hidden ${s.wrapper}`}
      style={{
        backgroundImage: hasBg ? `url(${backgroundImage})` : undefined,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
      }}
    >
      {hasBg && <div className="absolute inset-0 bg-black/55" />}
      <div
        className={`relative z-10 max-w-5xl mx-auto px-6 py-20 md:py-28 flex flex-col ${alignMap[align]}`}
      >
        <h2 className={`font-serif text-3xl md:text-5xl lg:text-6xl font-light leading-tight mb-5 ${effectiveHeading}`}>
          {heading}
        </h2>
        {subtitle && (
          <p className={`font-sans text-base md:text-lg max-w-2xl mb-10 leading-relaxed ${effectiveSubtitle}`}>
            {subtitle}
          </p>
        )}
        <div className={`flex flex-wrap gap-4 ${align === 'center' ? 'justify-center' : 'justify-start'}`}>
          {ctaLabel && ctaUrl && (
            <a
              href={ctaUrl}
              className={`inline-block px-8 py-4 text-xs tracking-[0.25em] uppercase transition ${effectivePrimary}`}
            >
              {ctaLabel}
            </a>
          )}
          {secondaryCtaLabel && secondaryCtaUrl && (
            <a
              href={secondaryCtaUrl}
              className={`inline-block px-8 py-4 text-xs tracking-[0.25em] uppercase border transition ${effectiveSecondary}`}
            >
              {secondaryCtaLabel}
            </a>
          )}
        </div>
      </div>
    </section>
  );
};

export const CTA: { config: ComponentConfig<CTAProps> } = {
  config: {
    label: 'Call to Action',
    fields: {
      heading: { type: 'text', label: 'Título' },
      subtitle: { type: 'textarea', label: 'Subtítulo (opcional)' },
      ctaLabel: { type: 'text', label: 'Texto del botón principal' },
      ctaUrl: { type: 'text', label: 'URL del botón principal' },
      secondaryCtaLabel: { type: 'text', label: 'Texto del botón secundario' },
      secondaryCtaUrl: { type: 'text', label: 'URL del botón secundario' },
      backgroundImage: { type: 'text', label: 'Imagen de fondo (opcional)' },
      align: {
        type: 'radio',
        label: 'Alineación',
        options: [
          { label: 'Centro', value: 'center' },
          { label: 'Izquierda', value: 'left' },
        ],
      },
      variant: {
        type: 'select',
        label: 'Variante',
        options: [
          { label: 'Oscuro', value: 'dark' },
          { label: 'Claro', value: 'light' },
          { label: 'Transparente', value: 'transparent' },
        ],
      },
    },
    defaultProps: {
      heading: '¿Empezamos tu proyecto?',
      subtitle: 'Cuéntanos tu idea y te propondremos un camino claro, honesto y a tu medida.',
      ctaLabel: 'Contactar',
      ctaUrl: '#',
      secondaryCtaLabel: 'Ver proyectos',
      secondaryCtaUrl: '#',
      backgroundImage: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1920&q=80',
      align: 'center',
      variant: 'dark',
    },
    render: CTARender,
  },
};

import type { ComponentConfig } from '@measured/puck';

export type LogoGridProps = {
  heading?: string;
  logos: { image: string; alt: string; url?: string }[];
  columns: 3 | 4 | 5 | 6;
  grayscale: boolean;
  monochrome: boolean;
  animate: 'none' | 'marquee' | 'fade';
};

const columnsMap: Record<LogoGridProps['columns'], string> = {
  3: 'grid-cols-2 md:grid-cols-3',
  4: 'grid-cols-2 md:grid-cols-4',
  5: 'grid-cols-2 md:grid-cols-5',
  6: 'grid-cols-3 md:grid-cols-6',
};

const LogoGridRender = ({
  heading,
  logos,
  columns,
  grayscale,
  monochrome,
  animate,
}: LogoGridProps) => {
  const filterClass = [
    grayscale ? 'grayscale' : '',
    monochrome ? 'brightness-0 invert-0 opacity-70' : '',
    'hover:grayscale-0 hover:opacity-100 transition duration-500',
  ].join(' ');

  const renderLogo = (logo: LogoGridProps['logos'][number], i: number) => {
    const img = (
      <img
        src={logo.image}
        alt={logo.alt}
        className={`max-h-12 md:max-h-16 w-auto object-contain ${filterClass}`}
      />
    );
    const wrapperClass = 'flex items-center justify-center px-6 py-4';
    if (logo.url) {
      return (
        <a key={i} href={logo.url} className={wrapperClass} target="_blank" rel="noreferrer">
          {img}
        </a>
      );
    }
    return (
      <div key={i} className={wrapperClass}>
        {img}
      </div>
    );
  };

  if (animate === 'marquee') {
    return (
      <section className="w-full bg-white py-20 overflow-hidden">
        {heading && (
          <div className="max-w-5xl mx-auto px-6 text-center mb-12">
            <h2 className="font-serif text-2xl md:text-3xl font-light text-black/80">
              {heading}
            </h2>
          </div>
        )}
        <div className="relative w-full overflow-hidden">
          <div
            className="flex gap-12 w-max"
            style={{ animation: 'logo-scroll 40s linear infinite' }}
          >
            {[...logos, ...logos].map((l, i) => renderLogo(l, i))}
          </div>
        </div>
        <style>{`
          @keyframes logo-scroll {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
          }
        `}</style>
      </section>
    );
  }

  return (
    <section className="w-full bg-white py-20">
      <div className="max-w-6xl mx-auto px-6">
        {heading && (
          <h2 className="font-serif text-2xl md:text-3xl font-light text-black/80 text-center mb-12">
            {heading}
          </h2>
        )}
        <div
          className={`grid ${columnsMap[columns]} gap-8 items-center ${
            animate === 'fade' ? 'animate-[fadeIn_1s_ease-in-out]' : ''
          }`}
        >
          {logos.map((l, i) => renderLogo(l, i))}
        </div>
      </div>
      <style>{`
        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(8px); }
          to { opacity: 1; transform: translateY(0); }
        }
      `}</style>
    </section>
  );
};

export const LogoGrid: { config: ComponentConfig<LogoGridProps> } = {
  config: {
    label: 'Logo Grid',
    fields: {
      heading: { type: 'text', label: 'Encabezado (opcional)' },
      logos: {
        type: 'array',
        label: 'Logos',
        arrayFields: {
          image: { type: 'text', label: 'URL imagen' },
          alt: { type: 'text', label: 'Texto alternativo' },
          url: { type: 'text', label: 'Enlace (opcional)' },
        },
      },
      columns: {
        type: 'select',
        label: 'Columnas',
        options: [
          { label: '3', value: 3 },
          { label: '4', value: 4 },
          { label: '5', value: 5 },
          { label: '6', value: 6 },
        ],
      },
      grayscale: {
        type: 'radio',
        label: 'Escala de grises',
        options: [
          { label: 'Sí', value: true },
          { label: 'No', value: false },
        ],
      },
      monochrome: {
        type: 'radio',
        label: 'Monocromo',
        options: [
          { label: 'Sí', value: true },
          { label: 'No', value: false },
        ],
      },
      animate: {
        type: 'select',
        label: 'Animación',
        options: [
          { label: 'Ninguna', value: 'none' },
          { label: 'Marquee', value: 'marquee' },
          { label: 'Fade', value: 'fade' },
        ],
      },
    },
    defaultProps: {
      heading: 'Confían en nosotros',
      logos: [
        { image: 'https://via.placeholder.com/200x80/0a0a0a/ffffff?text=AURORA', alt: 'Aurora' },
        { image: 'https://via.placeholder.com/200x80/0a0a0a/ffffff?text=MERIDIAN', alt: 'Meridian' },
        { image: 'https://via.placeholder.com/200x80/0a0a0a/ffffff?text=VESPER', alt: 'Vesper' },
        { image: 'https://via.placeholder.com/200x80/0a0a0a/ffffff?text=ATELIER', alt: 'Atelier' },
        { image: 'https://via.placeholder.com/200x80/0a0a0a/ffffff?text=NORTH', alt: 'North' },
        { image: 'https://via.placeholder.com/200x80/0a0a0a/ffffff?text=LUMEN', alt: 'Lumen' },
      ],
      columns: 6,
      grayscale: true,
      monochrome: false,
      animate: 'none',
    },
    render: LogoGridRender,
  },
};

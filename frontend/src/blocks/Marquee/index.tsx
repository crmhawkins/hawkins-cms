import type { ComponentConfig } from '@measured/puck';

export type MarqueeProps = {
  items: { text: string }[];
  speed: 'slow' | 'medium' | 'fast';
  fontSize: 'small' | 'medium' | 'large';
  background: 'transparent' | 'light' | 'dark';
  separator: 'dot' | 'slash' | 'dash';
};

const speedMap: Record<MarqueeProps['speed'], string> = {
  slow: '60s',
  medium: '40s',
  fast: '20s',
};

const fontSizeMap: Record<MarqueeProps['fontSize'], string> = {
  small: 'text-lg md:text-xl',
  medium: 'text-3xl md:text-5xl',
  large: 'text-5xl md:text-7xl',
};

const backgroundMap: Record<MarqueeProps['background'], string> = {
  transparent: 'bg-transparent text-black',
  light: 'bg-neutral-100 text-black',
  dark: 'bg-black text-white',
};

const separatorMap: Record<MarqueeProps['separator'], string> = {
  dot: '•',
  slash: '/',
  dash: '—',
};

const MarqueeRender = ({
  items,
  speed,
  fontSize,
  background,
  separator,
}: MarqueeProps) => {
  const sep = separatorMap[separator];
  const content = items.map((it, i) => (
    <span key={i} className="flex items-center gap-8 shrink-0 px-6">
      <span className="font-serif font-light whitespace-nowrap">{it.text}</span>
      <span className="opacity-40">{sep}</span>
    </span>
  ));

  return (
    <section className={`w-full py-10 overflow-hidden ${backgroundMap[background]}`}>
      <div className="relative w-full overflow-hidden">
        <div
          className={`flex w-max ${fontSizeMap[fontSize]}`}
          style={{ animation: `scroll-x ${speedMap[speed]} linear infinite` }}
        >
          {content}
          {content}
        </div>
      </div>
      <style>{`
        @keyframes scroll-x {
          from { transform: translateX(0); }
          to { transform: translateX(-50%); }
        }
      `}</style>
    </section>
  );
};

export const Marquee: { config: ComponentConfig<MarqueeProps> } = {
  config: {
    label: 'Marquee',
    fields: {
      items: {
        type: 'array',
        label: 'Elementos',
        arrayFields: {
          text: { type: 'text', label: 'Texto' },
        },
      },
      speed: {
        type: 'select',
        label: 'Velocidad',
        options: [
          { label: 'Lenta', value: 'slow' },
          { label: 'Media', value: 'medium' },
          { label: 'Rápida', value: 'fast' },
        ],
      },
      fontSize: {
        type: 'select',
        label: 'Tamaño fuente',
        options: [
          { label: 'Pequeño', value: 'small' },
          { label: 'Mediano', value: 'medium' },
          { label: 'Grande', value: 'large' },
        ],
      },
      background: {
        type: 'select',
        label: 'Fondo',
        options: [
          { label: 'Transparente', value: 'transparent' },
          { label: 'Claro', value: 'light' },
          { label: 'Oscuro', value: 'dark' },
        ],
      },
      separator: {
        type: 'radio',
        label: 'Separador',
        options: [
          { label: 'Punto', value: 'dot' },
          { label: 'Barra', value: 'slash' },
          { label: 'Guión', value: 'dash' },
        ],
      },
    },
    defaultProps: {
      items: [
        { text: 'Diseño' },
        { text: 'Artesanía' },
        { text: 'Luxury' },
        { text: 'Atemporal' },
        { text: 'Detalle' },
        { text: 'Precisión' },
      ],
      speed: 'medium',
      fontSize: 'large',
      background: 'dark',
      separator: 'dot',
    },
    render: MarqueeRender,
  },
};

import type { ComponentConfig } from '@measured/puck';

export type MapEmbedProps = {
  heading?: string;
  address: string;
  latitude?: number;
  longitude?: number;
  zoom: number;
  height: 'small' | 'medium' | 'large';
  style: 'default' | 'dark' | 'light';
};

const heightMap: Record<MapEmbedProps['height'], string> = {
  small: 'h-[300px]',
  medium: 'h-[480px]',
  large: 'h-[640px]',
};

const styleMap: Record<MapEmbedProps['style'], string> = {
  default: '',
  dark: 'grayscale contrast-125 brightness-75 invert',
  light: 'grayscale brightness-110',
};

const MapEmbedRender = ({
  heading,
  address,
  latitude,
  longitude,
  zoom,
  height,
  style,
}: MapEmbedProps) => {
  const lat = typeof latitude === 'number' ? latitude : 40.4168;
  const lng = typeof longitude === 'number' ? longitude : -3.7038;
  const span = Math.max(0.01, 0.5 / Math.max(1, zoom / 6));
  const bbox = `${lng - span},${lat - span},${lng + span},${lat + span}`;
  const src = `https://www.openstreetmap.org/export/embed.html?bbox=${bbox}&layer=mapnik&marker=${lat},${lng}`;

  return (
    <section className="w-full bg-white py-20">
      <div className="max-w-6xl mx-auto px-6">
        {heading && (
          <h2 className="font-serif text-3xl md:text-4xl font-light text-black mb-4 text-center">
            {heading}
          </h2>
        )}
        {address && (
          <p className="text-sm tracking-[0.2em] uppercase text-black/60 mb-10 text-center">
            {address}
          </p>
        )}
        <div className={`relative w-full ${heightMap[height]} overflow-hidden border border-black/10`}>
          <iframe
            src={src}
            className={`w-full h-full ${styleMap[style]}`}
            frameBorder={0}
            scrolling="no"
            loading="lazy"
          />
        </div>
      </div>
    </section>
  );
};

export const MapEmbed: { config: ComponentConfig<MapEmbedProps> } = {
  config: {
    label: 'Map Embed',
    fields: {
      heading: { type: 'text', label: 'Encabezado' },
      address: { type: 'text', label: 'Dirección' },
      latitude: { type: 'number', label: 'Latitud' },
      longitude: { type: 'number', label: 'Longitud' },
      zoom: { type: 'number', label: 'Zoom (1-20)', min: 1, max: 20 },
      height: {
        type: 'select',
        label: 'Altura',
        options: [
          { label: 'Pequeña', value: 'small' },
          { label: 'Mediana', value: 'medium' },
          { label: 'Grande', value: 'large' },
        ],
      },
      style: {
        type: 'select',
        label: 'Estilo',
        options: [
          { label: 'Default', value: 'default' },
          { label: 'Oscuro', value: 'dark' },
          { label: 'Claro', value: 'light' },
        ],
      },
    },
    defaultProps: {
      heading: 'Visítanos',
      address: 'Calle Gran Vía 28, Madrid',
      latitude: 40.4203,
      longitude: -3.7058,
      zoom: 15,
      height: 'medium',
      style: 'default',
    },
    render: MapEmbedRender,
  },
};

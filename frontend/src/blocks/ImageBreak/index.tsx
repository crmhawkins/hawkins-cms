import type { ComponentConfig } from '@measured/puck';

export type ImageBreakProps = {
  imageUrl: string;
  title?: string;
  subtitle?: string;
  height: 'small' | 'medium' | 'large';
  parallax: boolean;
  overlayOpacity: number;
};

const heightMap: Record<ImageBreakProps['height'], string> = {
  small: 'min-h-[40vh]',
  medium: 'min-h-[60vh]',
  large: 'min-h-[80vh]',
};

const ImageBreakRender = ({
  imageUrl,
  title,
  subtitle,
  height,
  parallax,
  overlayOpacity,
}: ImageBreakProps) => {
  return (
    <section
      className={`relative w-full flex items-center justify-center ${heightMap[height]} ${
        parallax ? 'bg-fixed' : ''
      } bg-cover bg-center overflow-hidden`}
      style={{ backgroundImage: `url(${imageUrl})` }}
    >
      <div
        className="absolute inset-0 bg-black"
        style={{ opacity: overlayOpacity / 100 }}
      />
      {(title || subtitle) && (
        <div className="relative z-10 max-w-3xl mx-auto px-6 text-center">
          {title && (
            <h2 className="font-serif text-4xl md:text-6xl font-light text-white mb-4 leading-tight">
              {title}
            </h2>
          )}
          {subtitle && (
            <p className="text-base md:text-lg text-white/80 leading-relaxed">
              {subtitle}
            </p>
          )}
        </div>
      )}
    </section>
  );
};

export const ImageBreak: { config: ComponentConfig<ImageBreakProps> } = {
  config: {
    label: 'Image Break',
    fields: {
      imageUrl: { type: 'text', label: 'URL de la imagen' },
      title: { type: 'text', label: 'Título' },
      subtitle: { type: 'textarea', label: 'Subtítulo' },
      height: {
        type: 'select',
        label: 'Altura',
        options: [
          { label: 'Pequeña', value: 'small' },
          { label: 'Mediana', value: 'medium' },
          { label: 'Grande', value: 'large' },
        ],
      },
      parallax: {
        type: 'radio',
        label: 'Parallax',
        options: [
          { label: 'Sí', value: true },
          { label: 'No', value: false },
        ],
      },
      overlayOpacity: {
        type: 'number',
        label: 'Opacidad overlay (0-100)',
        min: 0,
        max: 100,
      },
    },
    defaultProps: {
      imageUrl: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1920&q=80',
      title: 'Diseño atemporal',
      subtitle: 'Cada espacio cuenta una historia.',
      height: 'medium',
      parallax: true,
      overlayOpacity: 40,
    },
    render: ImageBreakRender,
  },
};

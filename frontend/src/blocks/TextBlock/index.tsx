import type { ComponentConfig } from '@measured/puck';

export type TextBlockProps = {
  heading?: string;
  body: string;
  align: 'left' | 'center' | 'right';
  maxWidth: 'sm' | 'md' | 'lg' | 'full';
  background: 'none' | 'light' | 'dark';
};

const widthMap = {
  sm: 'max-w-xl',
  md: 'max-w-3xl',
  lg: 'max-w-5xl',
  full: 'max-w-none',
};

const alignMap = {
  left: 'text-left',
  center: 'text-center',
  right: 'text-right',
};

const bgMap = {
  none: 'bg-transparent text-neutral-900',
  light: 'bg-neutral-50 text-neutral-900',
  dark: 'bg-neutral-950 text-white',
};

const TextBlockRender = ({ heading, body, align, maxWidth, background }: TextBlockProps) => {
  return (
    <section className={`w-full ${bgMap[background]}`}>
      <div className={`${widthMap[maxWidth]} mx-auto px-6 py-20 md:py-28 ${alignMap[align]}`}>
        {heading && (
          <h2 className="font-serif text-3xl md:text-5xl font-light mb-8 leading-tight">
            {heading}
          </h2>
        )}
        <div
          className="font-sans text-base md:text-lg leading-relaxed opacity-90 space-y-5 [&_a]:underline [&_strong]:font-medium"
          dangerouslySetInnerHTML={{ __html: body }}
        />
      </div>
    </section>
  );
};

export const TextBlock: { config: ComponentConfig<TextBlockProps> } = {
  config: {
    label: 'Texto',
    fields: {
      heading: { type: 'text', label: 'Título (opcional)' },
      body: { type: 'textarea', label: 'Cuerpo (admite HTML simple)' },
      align: {
        type: 'radio',
        label: 'Alineación',
        options: [
          { label: 'Izquierda', value: 'left' },
          { label: 'Centro', value: 'center' },
          { label: 'Derecha', value: 'right' },
        ],
      },
      maxWidth: {
        type: 'select',
        label: 'Ancho máximo',
        options: [
          { label: 'Pequeño', value: 'sm' },
          { label: 'Mediano', value: 'md' },
          { label: 'Grande', value: 'lg' },
          { label: 'Completo', value: 'full' },
        ],
      },
      background: {
        type: 'select',
        label: 'Fondo',
        options: [
          { label: 'Ninguno', value: 'none' },
          { label: 'Claro', value: 'light' },
          { label: 'Oscuro', value: 'dark' },
        ],
      },
    },
    defaultProps: {
      heading: 'Sobre nuestro estudio',
      body: '<p>Somos un estudio dedicado a crear espacios con carácter. Trabajamos con materiales nobles, proporciones cuidadas y una atención obsesiva al detalle.</p><p>Cada proyecto parte de una conversación, de escuchar a quien lo habitará. Lo demás es oficio, paciencia y la búsqueda de una belleza que no pasa de moda.</p>',
      align: 'left',
      maxWidth: 'md',
      background: 'none',
    },
    render: TextBlockRender,
  },
};

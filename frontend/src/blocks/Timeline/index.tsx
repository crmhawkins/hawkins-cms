import type { ComponentConfig } from '@measured/puck';

export type TimelineItem = {
  year: string;
  title: string;
  description: string;
};

export type TimelineProps = {
  heading?: string;
  subtitle?: string;
  items: TimelineItem[];
  orientation: 'horizontal' | 'vertical';
};

const TimelineRender = ({ heading, subtitle, items, orientation }: TimelineProps) => {
  return (
    <section className="w-full py-24 px-6 bg-white">
      <div className="max-w-6xl mx-auto">
        {heading && (
          <h2 className="font-serif text-3xl md:text-5xl font-light mb-4 text-center leading-tight text-neutral-900">
            {heading}
          </h2>
        )}
        {subtitle && (
          <p className="text-base text-neutral-600 text-center max-w-2xl mx-auto mb-16 leading-relaxed">
            {subtitle}
          </p>
        )}

        {orientation === 'vertical' ? (
          <div className="relative max-w-3xl mx-auto">
            <div className="absolute left-4 md:left-1/2 top-0 bottom-0 w-px bg-neutral-200" />
            <div className="space-y-12">
              {items.map((it, i) => (
                <div
                  key={i}
                  className={`relative flex md:items-center ${
                    i % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse'
                  }`}
                >
                  <div className="hidden md:block md:w-1/2" />
                  <div className="absolute left-4 md:left-1/2 -translate-x-1/2 w-3 h-3 rounded-full bg-neutral-900 border-4 border-white z-10" />
                  <div className="pl-12 md:pl-0 md:w-1/2 md:px-8">
                    <p className="font-serif text-3xl font-light text-neutral-900 mb-2">
                      {it.year}
                    </p>
                    <h3 className="text-sm tracking-[0.2em] uppercase text-neutral-500 mb-3">
                      {it.title}
                    </h3>
                    <p className="text-neutral-600 leading-relaxed">{it.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        ) : (
          <div className="relative">
            <div className="overflow-x-auto pb-6">
              <div className="flex gap-8 min-w-max relative pt-10">
                <div className="absolute top-[14px] left-4 right-4 h-px bg-neutral-200" />
                {items.map((it, i) => (
                  <div key={i} className="relative w-64 shrink-0">
                    <div className="absolute -top-10 left-0 w-3 h-3 rounded-full bg-neutral-900 border-4 border-white" />
                    <p className="font-serif text-3xl font-light text-neutral-900 mb-2">
                      {it.year}
                    </p>
                    <h3 className="text-sm tracking-[0.2em] uppercase text-neutral-500 mb-3">
                      {it.title}
                    </h3>
                    <p className="text-neutral-600 leading-relaxed text-sm">{it.description}</p>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}
      </div>
    </section>
  );
};

export const Timeline: { config: ComponentConfig<TimelineProps> } = {
  config: {
    label: 'Línea de tiempo',
    fields: {
      heading: { type: 'text', label: 'Título' },
      subtitle: { type: 'textarea', label: 'Subtítulo' },
      items: {
        type: 'array',
        label: 'Hitos',
        arrayFields: {
          year: { type: 'text', label: 'Año' },
          title: { type: 'text', label: 'Título' },
          description: { type: 'textarea', label: 'Descripción' },
        },
      },
      orientation: {
        type: 'radio',
        label: 'Orientación',
        options: [
          { label: 'Vertical', value: 'vertical' },
          { label: 'Horizontal', value: 'horizontal' },
        ],
      },
    },
    defaultProps: {
      heading: 'Nuestra historia',
      subtitle: 'Más de una década construyendo marcas memorables.',
      orientation: 'vertical',
      items: [
        {
          year: '2008',
          title: 'Fundación',
          description: 'Nace Hawkins con una visión clara: fusionar estrategia y estética para crear marcas atemporales.',
        },
        {
          year: '2013',
          title: 'Primera oficina',
          description: 'Abrimos nuestro estudio en el corazón de Madrid y ampliamos el equipo a 12 personas.',
        },
        {
          year: '2018',
          title: 'Expansión internacional',
          description: 'Comenzamos a trabajar con clientes en Milán, París y Nueva York.',
        },
        {
          year: '2024',
          title: 'Hawkins Digital',
          description: 'Lanzamos nuestra división digital dedicada a producto y plataformas a medida.',
        },
      ],
    },
    render: TimelineRender,
  },
};

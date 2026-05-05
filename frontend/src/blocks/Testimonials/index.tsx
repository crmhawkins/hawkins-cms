import type { ComponentConfig } from '@measured/puck';

export type TestimonialItem = {
  quote: string;
  author: string;
  role?: string;
  photo?: string;
};

export type TestimonialsProps = {
  heading?: string;
  items: TestimonialItem[];
  layout: 'grid' | 'slider';
  background: 'none' | 'light' | 'dark';
};

const bgMap = {
  none: 'bg-transparent text-neutral-900',
  light: 'bg-neutral-50 text-neutral-900',
  dark: 'bg-neutral-950 text-white',
};

const TestimonialsRender = ({ heading, items, layout, background }: TestimonialsProps) => {
  const isDark = background === 'dark';
  return (
    <section className={`w-full py-24 px-6 ${bgMap[background]}`}>
      <div className="max-w-6xl mx-auto">
        {heading && (
          <h2 className="font-serif text-3xl md:text-5xl font-light mb-16 text-center leading-tight">
            {heading}
          </h2>
        )}
        {layout === 'grid' ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {items.map((it, i) => (
              <figure
                key={i}
                className={`p-8 border ${isDark ? 'border-white/10 bg-white/5' : 'border-neutral-200 bg-white'}`}
              >
                <blockquote className="font-serif text-lg leading-relaxed mb-6 italic">
                  “{it.quote}”
                </blockquote>
                <figcaption className="flex items-center gap-4">
                  {it.photo && (
                    <img
                      src={it.photo}
                      alt={it.author}
                      className="w-12 h-12 rounded-full object-cover"
                    />
                  )}
                  <div>
                    <p className="text-sm font-medium">{it.author}</p>
                    {it.role && (
                      <p className={`text-xs ${isDark ? 'text-white/60' : 'text-neutral-500'}`}>
                        {it.role}
                      </p>
                    )}
                  </div>
                </figcaption>
              </figure>
            ))}
          </div>
        ) : (
          <div className="flex gap-8 overflow-x-auto snap-x snap-mandatory pb-4">
            {items.map((it, i) => (
              <figure
                key={i}
                className={`min-w-[320px] md:min-w-[480px] snap-center p-8 border ${isDark ? 'border-white/10 bg-white/5' : 'border-neutral-200 bg-white'}`}
              >
                <blockquote className="font-serif text-lg leading-relaxed mb-6 italic">
                  “{it.quote}”
                </blockquote>
                <figcaption className="flex items-center gap-4">
                  {it.photo && (
                    <img
                      src={it.photo}
                      alt={it.author}
                      className="w-12 h-12 rounded-full object-cover"
                    />
                  )}
                  <div>
                    <p className="text-sm font-medium">{it.author}</p>
                    {it.role && (
                      <p className={`text-xs ${isDark ? 'text-white/60' : 'text-neutral-500'}`}>
                        {it.role}
                      </p>
                    )}
                  </div>
                </figcaption>
              </figure>
            ))}
          </div>
        )}
      </div>
    </section>
  );
};

export const Testimonials: { config: ComponentConfig<TestimonialsProps> } = {
  config: {
    label: 'Testimonios',
    fields: {
      heading: { type: 'text', label: 'Título' },
      items: {
        type: 'array',
        label: 'Testimonios',
        arrayFields: {
          quote: { type: 'textarea', label: 'Cita' },
          author: { type: 'text', label: 'Autor' },
          role: { type: 'text', label: 'Cargo' },
          photo: { type: 'text', label: 'Foto (URL)' },
        },
      },
      layout: {
        type: 'radio',
        label: 'Layout',
        options: [
          { label: 'Cuadrícula', value: 'grid' },
          { label: 'Slider', value: 'slider' },
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
      heading: 'Lo que dicen nuestros clientes',
      layout: 'grid',
      background: 'light',
      items: [
        {
          quote: 'Trabajar con ellos transformó por completo nuestra presencia digital. Un equipo excepcional.',
          author: 'María González',
          role: 'CEO, Atelier Moda',
          photo: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=600&q=80',
        },
        {
          quote: 'Atención al detalle impecable y resultados que superaron todas nuestras expectativas.',
          author: 'Carlos Ruiz',
          role: 'Director, Grupo Hawkins',
          photo: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=600&q=80',
        },
        {
          quote: 'Profesionalidad de principio a fin. El proyecto se entregó en plazo y con una calidad excelente.',
          author: 'Elena Martín',
          role: 'Fundadora, Luxe Interiors',
          photo: 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=600&q=80',
        },
      ],
    },
    render: TestimonialsRender,
  },
};

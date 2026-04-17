import type { ComponentConfig } from '@measured/puck';

export type FAQItem = {
  question: string;
  answer: string;
};

export type FAQProps = {
  heading?: string;
  subtitle?: string;
  items: FAQItem[];
  columns: 1 | 2;
};

const FAQRender = ({ heading, subtitle, items, columns }: FAQProps) => {
  return (
    <section className="w-full py-24 px-6 bg-white">
      <div className="max-w-5xl mx-auto">
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
        <div className={`grid gap-4 ${columns === 2 ? 'md:grid-cols-2' : 'grid-cols-1'}`}>
          {items.map((it, i) => (
            <details
              key={i}
              className="group border-b border-neutral-200 py-6 [&_summary::-webkit-details-marker]:hidden"
            >
              <summary className="flex items-center justify-between cursor-pointer list-none">
                <span className="font-serif text-lg text-neutral-900 pr-8">{it.question}</span>
                <span className="shrink-0 w-6 h-6 flex items-center justify-center text-neutral-400 transition-transform group-open:rotate-45">
                  <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path d="M7 1V13M1 7H13" stroke="currentColor" strokeWidth="1.5" />
                  </svg>
                </span>
              </summary>
              <div className="mt-4 text-neutral-600 leading-relaxed">{it.answer}</div>
            </details>
          ))}
        </div>
      </div>
    </section>
  );
};

export const FAQ: { config: ComponentConfig<FAQProps> } = {
  config: {
    label: 'FAQ',
    fields: {
      heading: { type: 'text', label: 'Título' },
      subtitle: { type: 'textarea', label: 'Subtítulo' },
      items: {
        type: 'array',
        label: 'Preguntas',
        arrayFields: {
          question: { type: 'text', label: 'Pregunta' },
          answer: { type: 'textarea', label: 'Respuesta' },
        },
      },
      columns: {
        type: 'radio',
        label: 'Columnas',
        options: [
          { label: '1 columna', value: 1 },
          { label: '2 columnas', value: 2 },
        ],
      },
    },
    defaultProps: {
      heading: 'Preguntas frecuentes',
      subtitle: 'Resolvemos las dudas más comunes sobre nuestro proceso y servicios.',
      columns: 1,
      items: [
        {
          question: '¿Cuánto tiempo tarda un proyecto?',
          answer: 'Dependiendo del alcance, nuestros proyectos suelen completarse entre 4 y 12 semanas. Tras el briefing inicial te entregamos un calendario detallado.',
        },
        {
          question: '¿Cómo es el proceso de trabajo?',
          answer: 'Comenzamos con una fase de descubrimiento, seguida de estrategia, diseño, desarrollo y lanzamiento. Mantenemos reuniones semanales para que siempre estés al tanto.',
        },
        {
          question: '¿Ofrecéis mantenimiento posterior?',
          answer: 'Sí. Disponemos de planes de mantenimiento mensual que incluyen actualizaciones, soporte técnico y mejoras continuas.',
        },
        {
          question: '¿Qué incluye el presupuesto?',
          answer: 'Cada propuesta detalla las horas, entregables y plazos. No hay costes ocultos: lo que acordamos al inicio es lo que pagas.',
        },
      ],
    },
    render: FAQRender,
  },
};

import type { ComponentConfig } from '@measured/puck';
import { Check } from 'lucide-react';

export type PricingPlan = {
  name: string;
  price: string;
  period: string;
  description?: string;
  features: string[];
  ctaLabel: string;
  ctaUrl: string;
  highlighted: boolean;
};

export type PricingProps = {
  heading?: string;
  subtitle?: string;
  plans: PricingPlan[];
  columns: 2 | 3 | 4;
};

const colsMap = {
  2: 'md:grid-cols-2',
  3: 'md:grid-cols-2 lg:grid-cols-3',
  4: 'md:grid-cols-2 lg:grid-cols-4',
};

const PricingRender = ({ heading, subtitle, plans, columns }: PricingProps) => {
  return (
    <section className="w-full py-24 px-6 bg-neutral-50">
      <div className="max-w-7xl mx-auto">
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
        <div className={`grid grid-cols-1 gap-6 ${colsMap[columns]}`}>
          {plans.map((plan, i) => (
            <div
              key={i}
              className={`flex flex-col p-8 border transition ${
                plan.highlighted
                  ? 'bg-neutral-950 text-white border-neutral-950 scale-[1.02] shadow-2xl'
                  : 'bg-white text-neutral-900 border-neutral-200'
              }`}
            >
              <h3 className="font-serif text-2xl font-light mb-2">{plan.name}</h3>
              {plan.description && (
                <p className={`text-sm mb-6 ${plan.highlighted ? 'text-white/60' : 'text-neutral-500'}`}>
                  {plan.description}
                </p>
              )}
              <div className="mb-8 flex items-baseline gap-2">
                <span className="font-serif text-5xl font-light">{plan.price}</span>
                <span className={`text-sm ${plan.highlighted ? 'text-white/60' : 'text-neutral-500'}`}>
                  {plan.period}
                </span>
              </div>
              <ul className="flex-1 space-y-3 mb-8">
                {plan.features.map((f, j) => (
                  <li key={j} className="flex items-start gap-3 text-sm leading-relaxed">
                    <Check
                      size={16}
                      className={`mt-0.5 shrink-0 ${plan.highlighted ? 'text-white' : 'text-neutral-900'}`}
                    />
                    <span>{f}</span>
                  </li>
                ))}
              </ul>
              <a
                href={plan.ctaUrl}
                className={`inline-block text-center px-6 py-4 text-xs tracking-[0.25em] uppercase transition ${
                  plan.highlighted
                    ? 'bg-white text-black hover:bg-white/90'
                    : 'bg-neutral-900 text-white hover:bg-neutral-800'
                }`}
              >
                {plan.ctaLabel}
              </a>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export const Pricing: { config: ComponentConfig<PricingProps> } = {
  config: {
    label: 'Precios',
    fields: {
      heading: { type: 'text', label: 'Título' },
      subtitle: { type: 'textarea', label: 'Subtítulo' },
      plans: {
        type: 'array',
        label: 'Planes',
        arrayFields: {
          name: { type: 'text', label: 'Nombre del plan' },
          price: { type: 'text', label: 'Precio' },
          period: { type: 'text', label: 'Periodo' },
          description: { type: 'text', label: 'Descripción' },
          features: {
            type: 'array',
            label: 'Características',
            arrayFields: {
              item: { type: 'text', label: 'Característica' },
            } as any,
          } as any,
          ctaLabel: { type: 'text', label: 'Texto del botón' },
          ctaUrl: { type: 'text', label: 'URL del botón' },
          highlighted: {
            type: 'radio',
            label: 'Destacado',
            options: [
              { label: 'No', value: false },
              { label: 'Sí', value: true },
            ],
          },
        },
      },
      columns: {
        type: 'select',
        label: 'Columnas',
        options: [
          { label: '2 columnas', value: 2 },
          { label: '3 columnas', value: 3 },
          { label: '4 columnas', value: 4 },
        ],
      },
    },
    defaultProps: {
      heading: 'Planes a tu medida',
      subtitle: 'Elige la opción que mejor se adapte a tu proyecto. Sin permanencia, sin sorpresas.',
      columns: 3,
      plans: [
        {
          name: 'Essential',
          price: '€1.200',
          period: '/ mes',
          description: 'Ideal para marcas que empiezan.',
          features: ['Hasta 5 páginas', 'Diseño responsive', 'SEO básico', 'Soporte por email'],
          ctaLabel: 'Empezar',
          ctaUrl: '#',
          highlighted: false,
        },
        {
          name: 'Premium',
          price: '€2.800',
          period: '/ mes',
          description: 'El más elegido por nuestros clientes.',
          features: ['Páginas ilimitadas', 'Diseño personalizado', 'SEO avanzado', 'CMS a medida', 'Soporte prioritario'],
          ctaLabel: 'Elegir Premium',
          ctaUrl: '#',
          highlighted: true,
        },
        {
          name: 'Bespoke',
          price: '€5.500',
          period: '/ mes',
          description: 'Para proyectos sin límites.',
          features: ['Todo de Premium', 'Integraciones a medida', 'Estrategia dedicada', 'Consultoría mensual', 'SLA garantizado'],
          ctaLabel: 'Hablemos',
          ctaUrl: '#',
          highlighted: false,
        },
      ],
    },
    render: PricingRender,
  },
};

import type { ComponentConfig } from '@measured/puck';
import * as Icons from 'lucide-react';

export type FeatureItem = {
  icon?: string;
  title: string;
  description: string;
};

export type FeaturesProps = {
  heading?: string;
  subtitle?: string;
  items: FeatureItem[];
  columns: '2' | '3' | '4';
};

const colMap = {
  '2': 'md:grid-cols-2',
  '3': 'md:grid-cols-3',
  '4': 'md:grid-cols-4',
};

const FeaturesRender = ({ heading, subtitle, items, columns }: FeaturesProps) => {
  return (
    <section className="w-full bg-white">
      <div className="max-w-7xl mx-auto px-6 py-20 md:py-28">
        {(heading || subtitle) && (
          <div className="text-center max-w-3xl mx-auto mb-16">
            {heading && (
              <h2 className="font-serif text-3xl md:text-5xl font-light text-neutral-900 mb-5 leading-tight">
                {heading}
              </h2>
            )}
            {subtitle && (
              <p className="font-sans text-base md:text-lg text-neutral-600 leading-relaxed">
                {subtitle}
              </p>
            )}
          </div>
        )}

        <div className={`grid grid-cols-1 ${colMap[columns]} gap-10 md:gap-12`}>
          {items.map((item, i) => {
            const Icon = (item.icon && (Icons as any)[item.icon]) || Icons.Sparkles;
            return (
              <div key={i} className="text-left">
                <div className="w-12 h-12 flex items-center justify-center border border-neutral-200 text-neutral-900 mb-6">
                  <Icon className="w-5 h-5" strokeWidth={1.25} />
                </div>
                <h3 className="font-serif text-xl md:text-2xl font-light text-neutral-900 mb-3">
                  {item.title}
                </h3>
                <p className="font-sans text-sm md:text-base text-neutral-600 leading-relaxed">
                  {item.description}
                </p>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
};

export const Features: { config: ComponentConfig<FeaturesProps> } = {
  config: {
    label: 'Features',
    fields: {
      heading: { type: 'text', label: 'Título (opcional)' },
      subtitle: { type: 'textarea', label: 'Subtítulo (opcional)' },
      items: {
        type: 'array',
        label: 'Items',
        arrayFields: {
          icon: { type: 'text', label: 'Icono (lucide-react, ej. Sparkles)' },
          title: { type: 'text', label: 'Título' },
          description: { type: 'textarea', label: 'Descripción' },
        },
        defaultItemProps: {
          icon: 'Sparkles',
          title: 'Nuevo feature',
          description: 'Descripción corta.',
        },
      },
      columns: {
        type: 'radio',
        label: 'Columnas',
        options: [
          { label: '2', value: '2' },
          { label: '3', value: '3' },
          { label: '4', value: '4' },
        ],
      },
    },
    defaultProps: {
      heading: 'Un método, tres principios',
      subtitle: 'Trabajamos bajo unas mismas reglas en todos los proyectos, sin importar su escala.',
      items: [
        { icon: 'Compass', title: 'Diseño con propósito', description: 'Cada decisión responde a una intención. Nada sobra, nada falta.' },
        { icon: 'Hammer', title: 'Oficio y materialidad', description: 'Colaboramos con artesanos y proveedores que comparten nuestros estándares.' },
        { icon: 'Sparkles', title: 'Atención al detalle', description: 'La excelencia vive en lo pequeño: una junta, una costura, una luz.' },
      ],
      columns: '3',
    },
    render: FeaturesRender,
  },
};

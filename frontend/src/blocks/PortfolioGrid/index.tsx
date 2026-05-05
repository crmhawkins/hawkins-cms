import type { ComponentConfig } from '@measured/puck';
import { useMemo, useState } from 'react';

export type PortfolioItem = {
  title: string;
  category: string;
  image: string;
  url: string;
};

export type PortfolioGridProps = {
  heading?: string;
  subtitle?: string;
  source: 'manual' | 'collection';
  items: PortfolioItem[];
  columns: '2' | '3' | '4';
  showFilters: boolean;
};

const colMap = {
  '2': 'md:grid-cols-2',
  '3': 'md:grid-cols-3',
  '4': 'md:grid-cols-4',
};

const PortfolioGridRender = ({
  heading,
  subtitle,
  items,
  columns,
  showFilters,
}: PortfolioGridProps) => {
  const categories = useMemo(() => {
    const set = new Set<string>();
    (items || []).forEach((i) => i.category && set.add(i.category));
    return ['Todos', ...Array.from(set)];
  }, [items]);

  const [active, setActive] = useState('Todos');
  const filtered = active === 'Todos' ? items : items.filter((i) => i.category === active);

  return (
    <section className="w-full bg-white">
      <div className="max-w-7xl mx-auto px-6 py-20 md:py-28">
        {(heading || subtitle) && (
          <div className="text-center max-w-3xl mx-auto mb-12">
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

        {showFilters && categories.length > 1 && (
          <div className="flex flex-wrap justify-center gap-2 md:gap-3 mb-12">
            {categories.map((cat) => (
              <button
                key={cat}
                onClick={() => setActive(cat)}
                className={`px-5 py-2 text-xs tracking-[0.25em] uppercase border transition ${
                  active === cat
                    ? 'bg-neutral-900 text-white border-neutral-900'
                    : 'bg-transparent text-neutral-700 border-neutral-300 hover:border-neutral-900'
                }`}
              >
                {cat}
              </button>
            ))}
          </div>
        )}

        <div className={`grid grid-cols-1 ${colMap[columns]} gap-6 md:gap-8`}>
          {filtered.map((item, i) => (
            <a key={i} href={item.url} className="group block">
              <div className="aspect-[4/5] w-full overflow-hidden bg-neutral-100 mb-4">
                <img
                  src={item.image}
                  alt={item.title}
                  className="w-full h-full object-cover transition duration-700 group-hover:scale-105"
                />
              </div>
              <div>
                <p className="text-xs tracking-[0.25em] uppercase text-neutral-500 mb-1">
                  {item.category}
                </p>
                <h3 className="font-serif text-xl md:text-2xl font-light text-neutral-900 leading-tight">
                  {item.title}
                </h3>
              </div>
            </a>
          ))}
        </div>
      </div>
    </section>
  );
};

export const PortfolioGrid: { config: ComponentConfig<PortfolioGridProps> } = {
  config: {
    label: 'Portfolio Grid',
    fields: {
      heading: { type: 'text', label: 'Título (opcional)' },
      subtitle: { type: 'textarea', label: 'Subtítulo (opcional)' },
      source: {
        type: 'radio',
        label: 'Origen',
        options: [
          { label: 'Manual', value: 'manual' },
          { label: 'Colección (Directus)', value: 'collection' },
        ],
      },
      items: {
        type: 'array',
        label: 'Proyectos',
        arrayFields: {
          title: { type: 'text', label: 'Título' },
          category: { type: 'text', label: 'Categoría' },
          image: { type: 'text', label: 'Imagen (URL)' },
          url: { type: 'text', label: 'URL' },
        },
        defaultItemProps: {
          title: 'Nuevo proyecto',
          category: 'Interiorismo',
          image: 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=1200&q=80',
          url: '#',
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
      showFilters: {
        type: 'radio',
        label: 'Mostrar filtros',
        options: [
          { label: 'Sí', value: true },
          { label: 'No', value: false },
        ],
      },
    },
    defaultProps: {
      heading: 'Proyectos seleccionados',
      subtitle: 'Una muestra de nuestro trabajo reciente en interiorismo, arquitectura y branding.',
      source: 'manual',
      items: [
        { title: 'Villa Atalaya', category: 'Interiorismo', image: 'https://images.unsplash.com/photo-1615873968403-89e068629265?w=1200&q=80', url: '#' },
        { title: 'Casa Mediterránea', category: 'Arquitectura', image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1200&q=80', url: '#' },
        { title: 'Estudio Marbella', category: 'Interiorismo', image: 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&q=80', url: '#' },
        { title: 'Edificio Puerto', category: 'Arquitectura', image: 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?w=1200&q=80', url: '#' },
        { title: 'Boutique Sanzahra', category: 'Branding', image: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=1200&q=80', url: '#' },
        { title: 'Gala Benéfica', category: 'Eventos', image: 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&q=80', url: '#' },
      ],
      columns: '3',
      showFilters: true,
    },
    render: PortfolioGridRender,
  },
};

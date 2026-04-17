import type { ComponentConfig } from '@measured/puck';
import { useEffect, useRef, useState } from 'react';

export type StatItem = {
  value: number;
  label: string;
  suffix?: string;
};

export type StatsProps = {
  heading?: string;
  items: StatItem[];
  columns: 2 | 3 | 4;
  animate: boolean;
  background: 'dark' | 'light' | 'accent';
};

const colsMap = {
  2: 'md:grid-cols-2',
  3: 'md:grid-cols-3',
  4: 'md:grid-cols-2 lg:grid-cols-4',
};

const bgMap = {
  dark: 'bg-neutral-950 text-white',
  light: 'bg-neutral-50 text-neutral-900',
  accent: 'bg-[#b08d57] text-white',
};

const Counter = ({ value, suffix, animate }: { value: number; suffix?: string; animate: boolean }) => {
  const [display, setDisplay] = useState(animate ? 0 : value);
  const ref = useRef<HTMLSpanElement>(null);
  const started = useRef(false);

  useEffect(() => {
    if (!animate) {
      setDisplay(value);
      return;
    }
    const el = ref.current;
    if (!el) return;
    const obs = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting && !started.current) {
            started.current = true;
            const duration = 1600;
            const start = performance.now();
            const tick = (now: number) => {
              const t = Math.min(1, (now - start) / duration);
              const eased = 1 - Math.pow(1 - t, 3);
              setDisplay(Math.round(value * eased));
              if (t < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
          }
        });
      },
      { threshold: 0.3 },
    );
    obs.observe(el);
    return () => obs.disconnect();
  }, [value, animate]);

  return (
    <span ref={ref}>
      {display.toLocaleString('es-ES')}
      {suffix}
    </span>
  );
};

const StatsRender = ({ heading, items, columns, animate, background }: StatsProps) => {
  const subtleLabel = background === 'light' ? 'text-neutral-500' : 'text-white/70';
  return (
    <section className={`w-full py-24 px-6 ${bgMap[background]}`}>
      <div className="max-w-6xl mx-auto">
        {heading && (
          <h2 className="font-serif text-3xl md:text-5xl font-light mb-16 text-center leading-tight">
            {heading}
          </h2>
        )}
        <div className={`grid grid-cols-2 gap-10 ${colsMap[columns]}`}>
          {items.map((it, i) => (
            <div key={i} className="text-center">
              <p className="font-serif text-5xl md:text-6xl font-light mb-3 tracking-tight">
                <Counter value={it.value} suffix={it.suffix} animate={animate} />
              </p>
              <p className={`text-xs tracking-[0.25em] uppercase ${subtleLabel}`}>{it.label}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export const Stats: { config: ComponentConfig<StatsProps> } = {
  config: {
    label: 'Estadísticas',
    fields: {
      heading: { type: 'text', label: 'Título' },
      items: {
        type: 'array',
        label: 'Estadísticas',
        arrayFields: {
          value: { type: 'number', label: 'Valor numérico' },
          label: { type: 'text', label: 'Etiqueta' },
          suffix: { type: 'text', label: 'Sufijo (ej. %, +, k)' },
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
      animate: {
        type: 'radio',
        label: 'Animar contador',
        options: [
          { label: 'Sí', value: true },
          { label: 'No', value: false },
        ],
      },
      background: {
        type: 'select',
        label: 'Fondo',
        options: [
          { label: 'Oscuro', value: 'dark' },
          { label: 'Claro', value: 'light' },
          { label: 'Acento', value: 'accent' },
        ],
      },
    },
    defaultProps: {
      heading: 'Cifras que nos definen',
      columns: 4,
      animate: true,
      background: 'dark',
      items: [
        { value: 250, label: 'Proyectos entregados', suffix: '+' },
        { value: 18, label: 'Años de experiencia', suffix: '' },
        { value: 97, label: 'Clientes satisfechos', suffix: '%' },
        { value: 40, label: 'Premios recibidos', suffix: '' },
      ],
    },
    render: StatsRender,
  },
};

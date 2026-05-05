import type { ComponentConfig } from '@measured/puck';
import { useEffect, useState } from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

export type HeroSliderSlide = {
  image: string;
  title: string;
  subtitle?: string;
  ctaLabel?: string;
  ctaUrl?: string;
};

export type HeroSliderProps = {
  slides: HeroSliderSlide[];
  autoplay: boolean;
  intervalMs: number;
  overlay: number;
};

const HeroSliderRender = ({ slides, autoplay, intervalMs, overlay }: HeroSliderProps) => {
  const [index, setIndex] = useState(0);
  const total = slides?.length || 0;

  useEffect(() => {
    if (!autoplay || total <= 1) return;
    const id = setInterval(() => {
      setIndex((i) => (i + 1) % total);
    }, Math.max(2000, intervalMs || 5000));
    return () => clearInterval(id);
  }, [autoplay, intervalMs, total]);

  if (total === 0) {
    return (
      <section className="relative w-full min-h-screen bg-neutral-900 flex items-center justify-center text-white/60">
        Añade slides al HeroSlider
      </section>
    );
  }

  const go = (dir: number) => setIndex((i) => (i + dir + total) % total);
  const slide = slides[index];

  return (
    <section className="relative w-full min-h-screen overflow-hidden bg-black">
      {slides.map((s, i) => (
        <div
          key={i}
          className={`absolute inset-0 transition-opacity duration-1000 ${i === index ? 'opacity-100' : 'opacity-0'}`}
          style={{
            backgroundImage: `url(${s.image})`,
            backgroundSize: 'cover',
            backgroundPosition: 'center',
          }}
        />
      ))}
      <div className="absolute inset-0 bg-black" style={{ opacity: overlay / 100 }} />

      <div className="relative z-10 w-full min-h-screen flex flex-col justify-center items-center text-center px-6 py-24">
        <div className="max-w-4xl mx-auto">
          <h2 className="font-serif text-4xl md:text-6xl lg:text-7xl font-light text-white mb-6 leading-tight">
            {slide.title}
          </h2>
          {slide.subtitle && (
            <p className="text-base md:text-lg text-white/80 max-w-2xl mx-auto mb-8 leading-relaxed">
              {slide.subtitle}
            </p>
          )}
          {slide.ctaLabel && slide.ctaUrl && (
            <a
              href={slide.ctaUrl}
              className="inline-block bg-white text-black px-8 py-4 text-xs tracking-[0.25em] uppercase hover:bg-white/90 transition"
            >
              {slide.ctaLabel}
            </a>
          )}
        </div>
      </div>

      {total > 1 && (
        <>
          <button
            onClick={() => go(-1)}
            aria-label="Anterior"
            className="absolute left-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 flex items-center justify-center bg-white/10 hover:bg-white/20 border border-white/30 text-white transition"
          >
            <ChevronLeft className="w-5 h-5" />
          </button>
          <button
            onClick={() => go(1)}
            aria-label="Siguiente"
            className="absolute right-6 top-1/2 -translate-y-1/2 z-20 w-12 h-12 flex items-center justify-center bg-white/10 hover:bg-white/20 border border-white/30 text-white transition"
          >
            <ChevronRight className="w-5 h-5" />
          </button>

          <div className="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex gap-3">
            {slides.map((_, i) => (
              <button
                key={i}
                onClick={() => setIndex(i)}
                aria-label={`Slide ${i + 1}`}
                className={`w-2 h-2 rounded-full transition ${i === index ? 'bg-white w-8' : 'bg-white/40 hover:bg-white/60'}`}
              />
            ))}
          </div>
        </>
      )}
    </section>
  );
};

export const HeroSlider: { config: ComponentConfig<HeroSliderProps> } = {
  config: {
    label: 'Hero Slider',
    fields: {
      slides: {
        type: 'array',
        label: 'Slides',
        arrayFields: {
          image: { type: 'text', label: 'Imagen (URL)' },
          title: { type: 'text', label: 'Título' },
          subtitle: { type: 'textarea', label: 'Subtítulo' },
          ctaLabel: { type: 'text', label: 'Texto del botón' },
          ctaUrl: { type: 'text', label: 'URL del botón' },
        },
        defaultItemProps: {
          image: 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=1920&q=80',
          title: 'Nuevo slide',
          subtitle: '',
          ctaLabel: '',
          ctaUrl: '',
        },
      },
      autoplay: {
        type: 'radio',
        label: 'Reproducción automática',
        options: [
          { label: 'Sí', value: true },
          { label: 'No', value: false },
        ],
      },
      intervalMs: { type: 'number', label: 'Intervalo (ms)', min: 2000, max: 20000 },
      overlay: { type: 'number', label: 'Oscurecer imagen (0-100)', min: 0, max: 100 },
    },
    defaultProps: {
      slides: [
        {
          image: 'https://images.unsplash.com/photo-1615873968403-89e068629265?w=1920&q=80',
          title: 'Diseño que inspira',
          subtitle: 'Interiores únicos, pensados al detalle para quienes buscan lo excepcional.',
          ctaLabel: 'Descubrir',
          ctaUrl: '#',
        },
        {
          image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1920&q=80',
          title: 'Arquitectura atemporal',
          subtitle: 'Proyectos residenciales donde cada espacio cuenta una historia.',
          ctaLabel: 'Ver proyectos',
          ctaUrl: '#',
        },
        {
          image: 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?w=1920&q=80',
          title: 'Detalles que perduran',
          subtitle: 'La calidad no se improvisa: se construye con oficio y tiempo.',
          ctaLabel: 'Contacto',
          ctaUrl: '#',
        },
      ],
      autoplay: true,
      intervalMs: 5000,
      overlay: 45,
    },
    render: HeroSliderRender,
  },
};

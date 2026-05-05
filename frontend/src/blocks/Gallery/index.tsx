import type { ComponentConfig } from '@measured/puck';
import { useState } from 'react';
import { X, ChevronLeft, ChevronRight } from 'lucide-react';

export type GalleryImage = {
  url: string;
  alt: string;
  caption?: string;
};

export type GalleryProps = {
  images: GalleryImage[];
  columns: '2' | '3' | '4';
  gap: 'tight' | 'normal' | 'loose';
  lightbox: boolean;
};

const colMap = {
  '2': 'md:grid-cols-2',
  '3': 'md:grid-cols-3',
  '4': 'md:grid-cols-4',
};

const gapMap = {
  tight: 'gap-2',
  normal: 'gap-5',
  loose: 'gap-10',
};

const GalleryRender = ({ images, columns, gap, lightbox }: GalleryProps) => {
  const [open, setOpen] = useState<number | null>(null);
  const total = images?.length || 0;

  const close = () => setOpen(null);
  const go = (dir: number) => {
    if (open === null) return;
    setOpen(((open + dir) % total + total) % total);
  };

  return (
    <section className="w-full bg-white">
      <div className="max-w-7xl mx-auto px-6 py-20 md:py-24">
        <div className={`grid grid-cols-1 ${colMap[columns]} ${gapMap[gap]}`}>
          {images.map((img, i) => (
            <figure key={i} className="group">
              <button
                type="button"
                onClick={() => lightbox && setOpen(i)}
                className={`block w-full aspect-square overflow-hidden bg-neutral-100 ${lightbox ? 'cursor-zoom-in' : 'cursor-default'}`}
              >
                <img
                  src={img.url}
                  alt={img.alt}
                  className="w-full h-full object-cover transition duration-700 group-hover:scale-105"
                />
              </button>
              {img.caption && (
                <figcaption className="mt-3 text-xs tracking-[0.2em] uppercase text-neutral-500">
                  {img.caption}
                </figcaption>
              )}
            </figure>
          ))}
        </div>
      </div>

      {lightbox && open !== null && images[open] && (
        <div className="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-6" onClick={close}>
          <button
            onClick={close}
            aria-label="Cerrar"
            className="absolute top-6 right-6 w-12 h-12 flex items-center justify-center text-white hover:bg-white/10 border border-white/30"
          >
            <X className="w-5 h-5" />
          </button>
          <button
            onClick={(e) => { e.stopPropagation(); go(-1); }}
            aria-label="Anterior"
            className="absolute left-6 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center text-white hover:bg-white/10 border border-white/30"
          >
            <ChevronLeft className="w-5 h-5" />
          </button>
          <button
            onClick={(e) => { e.stopPropagation(); go(1); }}
            aria-label="Siguiente"
            className="absolute right-6 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center text-white hover:bg-white/10 border border-white/30"
          >
            <ChevronRight className="w-5 h-5" />
          </button>
          <img
            src={images[open].url}
            alt={images[open].alt}
            onClick={(e) => e.stopPropagation()}
            className="max-w-[90vw] max-h-[85vh] object-contain"
          />
        </div>
      )}
    </section>
  );
};

export const Gallery: { config: ComponentConfig<GalleryProps> } = {
  config: {
    label: 'Galería',
    fields: {
      images: {
        type: 'array',
        label: 'Imágenes',
        arrayFields: {
          url: { type: 'text', label: 'URL' },
          alt: { type: 'text', label: 'Texto alternativo' },
          caption: { type: 'text', label: 'Pie de foto (opcional)' },
        },
        defaultItemProps: {
          url: 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=1200&q=80',
          alt: 'Imagen',
          caption: '',
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
      gap: {
        type: 'select',
        label: 'Espaciado',
        options: [
          { label: 'Apretado', value: 'tight' },
          { label: 'Normal', value: 'normal' },
          { label: 'Amplio', value: 'loose' },
        ],
      },
      lightbox: {
        type: 'radio',
        label: 'Lightbox al hacer clic',
        options: [
          { label: 'Sí', value: true },
          { label: 'No', value: false },
        ],
      },
    },
    defaultProps: {
      images: [
        { url: 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=1200&q=80', alt: 'Interior', caption: 'Salón principal' },
        { url: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1200&q=80', alt: 'Vivienda moderna', caption: 'Fachada' },
        { url: 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&q=80', alt: 'Sofá', caption: 'Estar' },
        { url: 'https://images.unsplash.com/photo-1615873968403-89e068629265?w=1200&q=80', alt: 'Habitación elegante', caption: 'Dormitorio' },
        { url: 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=1200&q=80', alt: 'Sala', caption: 'Biblioteca' },
        { url: 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?w=1200&q=80', alt: 'Arquitectura', caption: 'Exterior' },
      ],
      columns: '3',
      gap: 'normal',
      lightbox: true,
    },
    render: GalleryRender,
  },
};

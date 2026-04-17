'use client';

import { useEffect, useState } from 'react';

/**
 * Tour de onboarding para el primer login del cliente.
 * Se muestra solo una vez y se guarda en localStorage.
 *
 * Para resetear (testing): localStorage.removeItem('hawkins_onboarded')
 */

const STEPS = [
  {
    title: '¡Bienvenido a tu CMS!',
    body: 'Te llevaremos 90 segundos a ver lo básico. Podrás volver a este tour cuando quieras desde Ajustes.',
    cta: 'Empezar',
  },
  {
    title: 'Páginas',
    body: 'Aquí vive el corazón de tu web: tus páginas. Puedes crear, editar y publicar tantas como necesites. Cada una se edita con nuestro editor visual (arrastra, suelta, escribe).',
    cta: 'Siguiente',
  },
  {
    title: 'Cabecera y pie de página',
    body: 'Desde "Headers" y "Footers" diseñas las barras que rodean tus páginas. Puedes tener varias versiones y elegir cuál usa cada página.',
    cta: 'Siguiente',
  },
  {
    title: 'Portfolio y Blog',
    body: 'Si tu negocio muestra proyectos o publica noticias, las colecciones Projects y Posts te permiten añadir entradas sin tocar páginas.',
    cta: 'Siguiente',
  },
  {
    title: 'Subir imágenes',
    body: 'Las imágenes van a la biblioteca multimedia. Arrastra y suelta fotos, las recortaremos automáticamente para cada lugar donde las uses.',
    cta: 'Siguiente',
  },
  {
    title: 'Traducir a otros idiomas',
    body: 'Al editar, verás un botón "Traducir" que genera automáticamente versiones en otros idiomas. Podrás retocarlas después a mano.',
    cta: 'Siguiente',
  },
  {
    title: 'Todo listo',
    body: 'Si tienes dudas, mira la ayuda contextual (icono ?) en cada sección. Y si te atascas, escríbenos. ¡A crear!',
    cta: 'Empezar a editar',
  },
];

export function OnboardingTour() {
  const [step, setStep] = useState(0);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    const done = localStorage.getItem('hawkins_onboarded');
    if (!done) setOpen(true);
  }, []);

  const finish = () => {
    localStorage.setItem('hawkins_onboarded', '1');
    setOpen(false);
  };

  const next = () => {
    if (step < STEPS.length - 1) setStep(step + 1);
    else finish();
  };

  if (!open) return null;

  const s = STEPS[step];
  const progress = ((step + 1) / STEPS.length) * 100;

  return (
    <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm">
      <div className="bg-white rounded-xl max-w-lg w-[90%] p-8 shadow-2xl animate-in fade-in zoom-in">
        {/* Progress bar */}
        <div className="w-full h-1 bg-neutral-100 rounded-full mb-6 overflow-hidden">
          <div
            className="h-full bg-black transition-all duration-500"
            style={{ width: `${progress}%` }}
          />
        </div>

        <p className="text-xs tracking-[0.2em] uppercase text-neutral-400 mb-2">
          Paso {step + 1} de {STEPS.length}
        </p>
        <h2 className="font-serif text-3xl mb-4 leading-tight">{s.title}</h2>
        <p className="text-neutral-600 leading-relaxed mb-8">{s.body}</p>

        <div className="flex items-center justify-between">
          <button
            onClick={finish}
            className="text-sm text-neutral-500 hover:text-black transition"
          >
            Saltar tour
          </button>
          <button
            onClick={next}
            className="bg-black text-white px-6 py-3 text-xs tracking-[0.2em] uppercase hover:bg-neutral-800 transition"
          >
            {s.cta}
          </button>
        </div>
      </div>
    </div>
  );
}

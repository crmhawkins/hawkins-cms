'use client';

import type { ComponentConfig } from '@measured/puck';
import { useEffect, useState } from 'react';

export type MemberGateProps = {
  heading: string;
  body: string;
  requiredTier: 'free' | 'premium' | 'vip';
  ctaLoginLabel: string;
  ctaRegisterLabel: string;
  // Contenido protegido: como HTML plano por simplicidad editable en Puck
  protectedHtml?: string;
};

const TIER_RANK = { free: 0, premium: 1, vip: 2 } as const;
const TIER_LABEL = { free: 'Free', premium: 'Premium', vip: 'VIP' } as const;

type Me = { id: string; tier: 'free' | 'premium' | 'vip' } | null;

const MemberGateRender = ({
  heading,
  body,
  requiredTier,
  ctaLoginLabel,
  ctaRegisterLabel,
  protectedHtml,
}: MemberGateProps) => {
  const [me, setMe] = useState<Me | undefined>(undefined);

  useEffect(() => {
    let mounted = true;
    (async () => {
      try {
        const res = await fetch('/api/members/me', { cache: 'no-store' });
        if (!res.ok) {
          if (mounted) setMe(null);
          return;
        }
        const data = await res.json();
        if (mounted) setMe(data.member);
      } catch {
        if (mounted) setMe(null);
      }
    })();
    return () => {
      mounted = false;
    };
  }, []);

  if (me === undefined) {
    return (
      <section className="py-16 px-6 text-center text-sm text-neutral-500">
        Cargando…
      </section>
    );
  }

  const canView = me && TIER_RANK[me.tier] >= TIER_RANK[requiredTier];

  if (canView) {
    return (
      <section className="py-12 px-6">
        <div
          className="max-w-3xl mx-auto prose prose-neutral"
          dangerouslySetInnerHTML={{ __html: protectedHtml || '' }}
        />
      </section>
    );
  }

  return (
    <section className="py-16 px-6 bg-neutral-50">
      <div className="max-w-xl mx-auto bg-white border border-neutral-200 p-10 text-center">
        <p className="text-xs tracking-[0.3em] uppercase text-neutral-500 mb-3">
          {TIER_LABEL[requiredTier]} o superior
        </p>
        <h3 className="font-serif text-2xl text-neutral-900 mb-3">{heading}</h3>
        <p className="text-sm text-neutral-600 mb-8 leading-relaxed">{body}</p>
        <div className="flex flex-col sm:flex-row gap-3 justify-center">
          <a
            href="/miembros/login"
            className="inline-block bg-neutral-900 text-white px-6 py-3 text-xs tracking-[0.25em] uppercase hover:bg-neutral-700 transition"
          >
            {ctaLoginLabel}
          </a>
          <a
            href="/miembros/registro"
            className="inline-block border border-neutral-900 text-neutral-900 px-6 py-3 text-xs tracking-[0.25em] uppercase hover:bg-neutral-900 hover:text-white transition"
          >
            {ctaRegisterLabel}
          </a>
        </div>
      </div>
    </section>
  );
};

export const MemberGate: { config: ComponentConfig<MemberGateProps> } = {
  config: {
    label: 'Member Gate',
    fields: {
      heading: { type: 'text', label: 'Título' },
      body: { type: 'textarea', label: 'Texto' },
      requiredTier: {
        type: 'select',
        label: 'Nivel requerido',
        options: [
          { label: 'Free', value: 'free' },
          { label: 'Premium', value: 'premium' },
          { label: 'VIP', value: 'vip' },
        ],
      },
      ctaLoginLabel: { type: 'text', label: 'Texto botón login' },
      ctaRegisterLabel: { type: 'text', label: 'Texto botón registro' },
      protectedHtml: { type: 'textarea', label: 'Contenido protegido (HTML)' },
    },
    defaultProps: {
      heading: 'Contenido exclusivo para miembros',
      body: 'Accede a este contenido con tu cuenta. Si aún no eres miembro, regístrate gratis.',
      requiredTier: 'free',
      ctaLoginLabel: 'Acceder',
      ctaRegisterLabel: 'Registrarme',
      protectedHtml: '<p>Contenido visible solo para miembros.</p>',
    },
    render: MemberGateRender,
  },
};

'use client';

import { useEffect, useState } from 'react';
import { useParams, useRouter } from 'next/navigation';
import { Puck, type Data } from '@measured/puck';
import '@measured/puck/puck.css';
import { puckConfig } from '@/blocks';
import { OnboardingTour } from '@/components/OnboardingTour';

/**
 * Editor visual Puck para páginas del CMS.
 * URL: /editor/[page-id]
 *
 * Carga la página por ID, permite editar con drag & drop y guardar.
 * Requiere estar autenticado (cookie `directus_session_token`).
 */
export default function PageEditor() {
  const params = useParams();
  const router = useRouter();
  const id = params.id as string;

  const [data, setData] = useState<Data | null>(null);
  const [pageMeta, setPageMeta] = useState<{ title?: string; slug?: string } | null>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const load = async () => {
      try {
        const res = await fetch(`/api/pages/${id}`, { credentials: 'include' });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const page = await res.json();
        setPageMeta({ title: page.title, slug: page.slug });
        setData(
          page.content ?? {
            content: [],
            root: { props: { title: page.title || '' } },
            zones: {},
          }
        );
      } catch (e: any) {
        setError(e.message || 'Error cargando página');
      } finally {
        setLoading(false);
      }
    };
    load();
  }, [id]);

  const handleSave = async (newData: Data) => {
    setSaving(true);
    setError(null);
    try {
      const res = await fetch(`/api/pages/${id}`, {
        method: 'PATCH',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ content: newData }),
      });
      if (!res.ok) throw new Error(`Save failed: ${res.status}`);
      // Toast simple
      alert('✓ Página guardada');
    } catch (e: any) {
      setError(e.message || 'Error guardando');
    } finally {
      setSaving(false);
    }
  };

  const handlePublish = async (newData: Data) => {
    await handleSave(newData);
    await fetch(`/api/pages/${id}/publish`, {
      method: 'POST',
      credentials: 'include',
    });
    alert('✓ Página publicada');
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-neutral-100">
        <p className="text-sm text-neutral-500 tracking-wider uppercase">Cargando editor…</p>
      </div>
    );
  }

  if (error && !data) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-neutral-100">
        <div className="text-center max-w-md">
          <p className="text-red-600 mb-4">{error}</p>
          <button onClick={() => router.push('/admin')} className="text-sm underline">
            Volver al panel
          </button>
        </div>
      </div>
    );
  }

  return (
    <>
      {/* Barra superior custom */}
      <div className="fixed top-0 left-0 right-0 z-[101] bg-black text-white px-4 py-2 flex items-center justify-between border-b border-white/10">
        <div className="flex items-center gap-4">
          <button
            onClick={() => router.push('/admin/content/pages')}
            className="text-xs tracking-wider uppercase text-white/70 hover:text-white"
          >
            ← Panel
          </button>
          <span className="text-sm font-serif">{pageMeta?.title || 'Página'}</span>
          <span className="text-xs text-white/40">/{pageMeta?.slug}</span>
        </div>
        <div className="flex items-center gap-2">
          {saving && <span className="text-xs text-white/60">Guardando…</span>}
          <a
            href={`/${pageMeta?.slug === 'home' ? '' : pageMeta?.slug}`}
            target="_blank"
            rel="noopener"
            className="text-xs px-3 py-1.5 border border-white/20 hover:bg-white/10 transition"
          >
            Ver en el sitio
          </a>
        </div>
      </div>

      <div className="pt-10">
        <Puck
          config={puckConfig}
          data={data!}
          onPublish={handlePublish}
          headerTitle={pageMeta?.title || 'Editor'}
        />
      </div>

      <OnboardingTour />
    </>
  );
}

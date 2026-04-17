'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';

type Me = {
  id: string;
  email: string;
  name: string;
  tier: string;
  avatar: string | null;
};

export default function ProfilePage() {
  const router = useRouter();
  const [me, setMe] = useState<Me | null>(null);
  const [loadingMe, setLoadingMe] = useState(true);
  const [name, setName] = useState('');
  const [avatar, setAvatar] = useState('');
  const [currentPw, setCurrentPw] = useState('');
  const [newPw, setNewPw] = useState('');
  const [msg, setMsg] = useState<{ type: 'ok' | 'err'; text: string } | null>(null);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    (async () => {
      try {
        const res = await fetch('/api/members/me', { cache: 'no-store' });
        if (!res.ok) {
          router.push('/miembros/login');
          return;
        }
        const data = await res.json();
        setMe(data.member);
        setName(data.member.name || '');
        setAvatar(data.member.avatar || '');
      } finally {
        setLoadingMe(false);
      }
    })();
  }, [router]);

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setMsg(null);
    setSaving(true);
    const body: Record<string, any> = { name, avatar: avatar || null };
    if (newPw) {
      body.new_password = newPw;
      body.current_password = currentPw;
    }
    try {
      const res = await fetch('/api/members/update-profile', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data.ok) {
        setMsg({ type: 'err', text: data.error || 'No se pudo guardar' });
      } else {
        setMsg({ type: 'ok', text: 'Cambios guardados' });
        setCurrentPw('');
        setNewPw('');
      }
    } catch {
      setMsg({ type: 'err', text: 'Error de red' });
    }
    setSaving(false);
  }

  if (loadingMe) {
    return (
      <main className="min-h-screen flex items-center justify-center bg-neutral-50">
        <p className="text-sm text-neutral-500">Cargando…</p>
      </main>
    );
  }
  if (!me) return null;

  return (
    <main className="min-h-screen bg-neutral-50 px-6 py-16">
      <div className="max-w-2xl mx-auto">
        <div className="mb-8">
          <Link
            href="/miembros"
            className="text-xs tracking-[0.2em] uppercase text-neutral-500 hover:text-neutral-900"
          >
            ← Volver
          </Link>
        </div>
        <h1 className="font-serif text-4xl text-neutral-900 mb-8">Mi perfil</h1>
        <div className="bg-white border border-neutral-200 p-8">
          <form onSubmit={onSubmit} className="space-y-6">
            <label className="block">
              <span className="text-xs tracking-[0.2em] uppercase text-neutral-600">Email</span>
              <input
                type="email"
                value={me.email}
                disabled
                className="mt-2 w-full border border-neutral-200 bg-neutral-100 px-4 py-3 text-sm text-neutral-500"
              />
            </label>
            <label className="block">
              <span className="text-xs tracking-[0.2em] uppercase text-neutral-600">Nombre</span>
              <input
                type="text"
                required
                value={name}
                onChange={(e) => setName(e.target.value)}
                className="mt-2 w-full border border-neutral-300 px-4 py-3 text-sm focus:outline-none focus:border-neutral-900"
              />
            </label>
            <label className="block">
              <span className="text-xs tracking-[0.2em] uppercase text-neutral-600">Avatar (URL)</span>
              <input
                type="text"
                value={avatar}
                onChange={(e) => setAvatar(e.target.value)}
                placeholder="https://…"
                className="mt-2 w-full border border-neutral-300 px-4 py-3 text-sm focus:outline-none focus:border-neutral-900"
              />
            </label>

            <div className="pt-6 border-t border-neutral-200 space-y-5">
              <h2 className="text-xs tracking-[0.25em] uppercase text-neutral-600">
                Cambiar contraseña
              </h2>
              <label className="block">
                <span className="text-xs tracking-[0.2em] uppercase text-neutral-600">
                  Contraseña actual
                </span>
                <input
                  type="password"
                  autoComplete="current-password"
                  value={currentPw}
                  onChange={(e) => setCurrentPw(e.target.value)}
                  className="mt-2 w-full border border-neutral-300 px-4 py-3 text-sm focus:outline-none focus:border-neutral-900"
                />
              </label>
              <label className="block">
                <span className="text-xs tracking-[0.2em] uppercase text-neutral-600">
                  Nueva contraseña
                </span>
                <input
                  type="password"
                  autoComplete="new-password"
                  minLength={8}
                  value={newPw}
                  onChange={(e) => setNewPw(e.target.value)}
                  className="mt-2 w-full border border-neutral-300 px-4 py-3 text-sm focus:outline-none focus:border-neutral-900"
                />
              </label>
            </div>

            {msg && (
              <p className={`text-sm ${msg.type === 'ok' ? 'text-green-700' : 'text-red-600'}`}>
                {msg.text}
              </p>
            )}
            <button
              type="submit"
              disabled={saving}
              className="w-full bg-neutral-900 text-white py-3 text-xs tracking-[0.25em] uppercase hover:bg-neutral-700 transition disabled:opacity-50"
            >
              {saving ? 'Guardando…' : 'Guardar cambios'}
            </button>
          </form>
        </div>
      </div>
    </main>
  );
}

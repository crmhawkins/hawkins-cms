'use client';

import { useState } from 'react';
import { useParams, useRouter } from 'next/navigation';
import Link from 'next/link';

export default function ResetPage() {
  const params = useParams<{ token: string }>();
  const token = params?.token as string;
  const router = useRouter();
  const [password, setPassword] = useState('');
  const [confirm, setConfirm] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [done, setDone] = useState(false);

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(null);
    if (password.length < 8) {
      setError('La contraseña debe tener al menos 8 caracteres');
      return;
    }
    if (password !== confirm) {
      setError('Las contraseñas no coinciden');
      return;
    }
    setLoading(true);
    try {
      const res = await fetch('/api/members/apply-reset', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ token, password }),
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data.ok) {
        setError(data.error || 'Token inválido o expirado');
        setLoading(false);
        return;
      }
      setDone(true);
      setLoading(false);
      setTimeout(() => router.push('/miembros/login'), 1800);
    } catch {
      setError('Error de red');
      setLoading(false);
    }
  }

  return (
    <main className="min-h-screen flex items-center justify-center bg-neutral-50 px-6 py-16">
      <div className="w-full max-w-md bg-white border border-neutral-200 p-10">
        <h1 className="font-serif text-3xl mb-8 text-neutral-900">Nueva contraseña</h1>
        {done ? (
          <div className="space-y-4">
            <p className="text-sm text-neutral-700">
              Contraseña actualizada. Redirigiendo al login…
            </p>
            <Link
              href="/miembros/login"
              className="inline-block text-xs tracking-[0.2em] uppercase underline underline-offset-4 text-neutral-900"
            >
              Ir al login
            </Link>
          </div>
        ) : (
          <form onSubmit={onSubmit} className="space-y-5">
            <label className="block">
              <span className="text-xs tracking-[0.2em] uppercase text-neutral-600">Nueva contraseña</span>
              <input
                type="password"
                required
                minLength={8}
                autoComplete="new-password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                className="mt-2 w-full border border-neutral-300 px-4 py-3 text-sm focus:outline-none focus:border-neutral-900"
              />
            </label>
            <label className="block">
              <span className="text-xs tracking-[0.2em] uppercase text-neutral-600">Confirmar</span>
              <input
                type="password"
                required
                minLength={8}
                autoComplete="new-password"
                value={confirm}
                onChange={(e) => setConfirm(e.target.value)}
                className="mt-2 w-full border border-neutral-300 px-4 py-3 text-sm focus:outline-none focus:border-neutral-900"
              />
            </label>
            {error && <p className="text-sm text-red-600">{error}</p>}
            <button
              type="submit"
              disabled={loading}
              className="w-full bg-neutral-900 text-white py-3 text-xs tracking-[0.25em] uppercase hover:bg-neutral-700 transition disabled:opacity-50"
            >
              {loading ? 'Guardando…' : 'Guardar contraseña'}
            </button>
          </form>
        )}
      </div>
    </main>
  );
}

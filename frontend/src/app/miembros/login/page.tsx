'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError(null);
    setLoading(true);
    try {
      const res = await fetch('/api/members/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data.ok) {
        setError(data.error || 'Credenciales incorrectas');
        setLoading(false);
        return;
      }
      router.push('/miembros');
      router.refresh();
    } catch {
      setError('Error de red');
      setLoading(false);
    }
  }

  return (
    <main className="min-h-screen flex items-center justify-center bg-neutral-50 px-6 py-16">
      <div className="w-full max-w-md bg-white border border-neutral-200 p-10">
        <h1 className="font-serif text-3xl mb-8 text-neutral-900">Acceder</h1>
        <form onSubmit={onSubmit} className="space-y-5">
          <label className="block">
            <span className="text-xs tracking-[0.2em] uppercase text-neutral-600">Email</span>
            <input
              type="email"
              required
              autoComplete="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="mt-2 w-full border border-neutral-300 px-4 py-3 text-sm focus:outline-none focus:border-neutral-900"
            />
          </label>
          <label className="block">
            <span className="text-xs tracking-[0.2em] uppercase text-neutral-600">Contraseña</span>
            <input
              type="password"
              required
              autoComplete="current-password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="mt-2 w-full border border-neutral-300 px-4 py-3 text-sm focus:outline-none focus:border-neutral-900"
            />
          </label>
          {error && <p className="text-sm text-red-600">{error}</p>}
          <button
            type="submit"
            disabled={loading}
            className="w-full bg-neutral-900 text-white py-3 text-xs tracking-[0.25em] uppercase hover:bg-neutral-700 transition disabled:opacity-50"
          >
            {loading ? 'Entrando…' : 'Entrar'}
          </button>
        </form>
        <div className="mt-8 flex flex-col gap-2 text-sm text-neutral-600">
          <Link href="/miembros/registro" className="hover:text-neutral-900 underline underline-offset-4">
            Crear cuenta
          </Link>
          <Link href="/miembros/recuperar" className="hover:text-neutral-900 underline underline-offset-4">
            He olvidado mi contraseña
          </Link>
        </div>
      </div>
    </main>
  );
}

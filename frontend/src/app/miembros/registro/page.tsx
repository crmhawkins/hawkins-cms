'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';

export default function RegisterPage() {
  const router = useRouter();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirm, setConfirm] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

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
      const res = await fetch('/api/members/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, email, password }),
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data.ok) {
        setError(data.error || 'No se pudo crear la cuenta');
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
        <h1 className="font-serif text-3xl mb-8 text-neutral-900">Crear cuenta</h1>
        <form onSubmit={onSubmit} className="space-y-5">
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
              minLength={8}
              autoComplete="new-password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="mt-2 w-full border border-neutral-300 px-4 py-3 text-sm focus:outline-none focus:border-neutral-900"
            />
          </label>
          <label className="block">
            <span className="text-xs tracking-[0.2em] uppercase text-neutral-600">Confirmar contraseña</span>
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
            {loading ? 'Creando…' : 'Registrarme'}
          </button>
        </form>
        <p className="mt-8 text-sm text-neutral-600">
          ¿Ya tienes cuenta?{' '}
          <Link href="/miembros/login" className="hover:text-neutral-900 underline underline-offset-4">
            Acceder
          </Link>
        </p>
      </div>
    </main>
  );
}

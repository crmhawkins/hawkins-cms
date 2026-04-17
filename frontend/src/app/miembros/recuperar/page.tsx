'use client';

import { useState } from 'react';
import Link from 'next/link';

export default function RecoverPage() {
  const [email, setEmail] = useState('');
  const [loading, setLoading] = useState(false);
  const [sent, setSent] = useState(false);

  async function onSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    try {
      await fetch('/api/members/request-reset', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email }),
      });
    } catch {
      /* silent */
    }
    setSent(true);
    setLoading(false);
  }

  return (
    <main className="min-h-screen flex items-center justify-center bg-neutral-50 px-6 py-16">
      <div className="w-full max-w-md bg-white border border-neutral-200 p-10">
        <h1 className="font-serif text-3xl mb-8 text-neutral-900">Recuperar contraseña</h1>
        {sent ? (
          <div className="space-y-4">
            <p className="text-sm text-neutral-700 leading-relaxed">
              Si existe una cuenta con ese email, recibirás un mensaje con las instrucciones
              para restablecer tu contraseña. Revisa tu bandeja de entrada (y la carpeta de spam).
            </p>
            <Link
              href="/miembros/login"
              className="inline-block mt-4 text-xs tracking-[0.2em] uppercase underline underline-offset-4 text-neutral-900"
            >
              Volver al login
            </Link>
          </div>
        ) : (
          <form onSubmit={onSubmit} className="space-y-5">
            <label className="block">
              <span className="text-xs tracking-[0.2em] uppercase text-neutral-600">Email</span>
              <input
                type="email"
                required
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="mt-2 w-full border border-neutral-300 px-4 py-3 text-sm focus:outline-none focus:border-neutral-900"
              />
            </label>
            <button
              type="submit"
              disabled={loading}
              className="w-full bg-neutral-900 text-white py-3 text-xs tracking-[0.25em] uppercase hover:bg-neutral-700 transition disabled:opacity-50"
            >
              {loading ? 'Enviando…' : 'Enviar enlace'}
            </button>
          </form>
        )}
      </div>
    </main>
  );
}

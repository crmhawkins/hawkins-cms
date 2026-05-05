'use client';

import type { ComponentConfig } from '@measured/puck';
import { useState } from 'react';

export type MemberLoginFormProps = {
  heading: string;
  subtitle?: string;
  variant: 'centered' | 'card';
  redirectAfter: string;
};

const MemberLoginFormRender = ({
  heading,
  subtitle,
  variant,
  redirectAfter,
}: MemberLoginFormProps) => {
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
      window.location.href = redirectAfter || '/miembros';
    } catch {
      setError('Error de red');
      setLoading(false);
    }
  }

  const inner = (
    <>
      <h3 className="font-serif text-3xl text-neutral-900 mb-2">{heading}</h3>
      {subtitle && (
        <p className="text-sm text-neutral-600 mb-6 leading-relaxed">{subtitle}</p>
      )}
      <form onSubmit={onSubmit} className="space-y-4">
        <input
          type="email"
          required
          placeholder="Email"
          autoComplete="email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          className="w-full border border-neutral-300 px-4 py-3 text-sm focus:outline-none focus:border-neutral-900"
        />
        <input
          type="password"
          required
          placeholder="Contraseña"
          autoComplete="current-password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          className="w-full border border-neutral-300 px-4 py-3 text-sm focus:outline-none focus:border-neutral-900"
        />
        {error && <p className="text-sm text-red-600">{error}</p>}
        <button
          type="submit"
          disabled={loading}
          className="w-full bg-neutral-900 text-white py-3 text-xs tracking-[0.25em] uppercase hover:bg-neutral-700 transition disabled:opacity-50"
        >
          {loading ? 'Entrando…' : 'Entrar'}
        </button>
      </form>
      <div className="mt-6 flex justify-between text-xs text-neutral-600">
        <a href="/miembros/registro" className="hover:text-neutral-900 underline underline-offset-4">
          Crear cuenta
        </a>
        <a href="/miembros/recuperar" className="hover:text-neutral-900 underline underline-offset-4">
          Recuperar contraseña
        </a>
      </div>
    </>
  );

  if (variant === 'card') {
    return (
      <section className="py-16 px-6 bg-neutral-50">
        <div className="max-w-md mx-auto bg-white border border-neutral-200 p-10">{inner}</div>
      </section>
    );
  }
  return (
    <section className="py-16 px-6">
      <div className="max-w-md mx-auto text-center">{inner}</div>
    </section>
  );
};

export const MemberLoginForm: { config: ComponentConfig<MemberLoginFormProps> } = {
  config: {
    label: 'Member Login',
    fields: {
      heading: { type: 'text', label: 'Título' },
      subtitle: { type: 'textarea', label: 'Subtítulo' },
      variant: {
        type: 'radio',
        label: 'Estilo',
        options: [
          { label: 'Centrado', value: 'centered' },
          { label: 'Tarjeta', value: 'card' },
        ],
      },
      redirectAfter: { type: 'text', label: 'URL tras login' },
    },
    defaultProps: {
      heading: 'Acceder',
      subtitle: 'Introduce tus credenciales para acceder al área de miembros.',
      variant: 'card',
      redirectAfter: '/miembros',
    },
    render: MemberLoginFormRender,
  },
};

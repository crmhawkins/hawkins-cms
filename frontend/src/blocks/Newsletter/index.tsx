'use client';

import { useState } from 'react';
import type { ComponentConfig } from '@measured/puck';

export type NewsletterProps = {
  heading: string;
  subtitle?: string;
  placeholder: string;
  submitLabel: string;
  successMessage: string;
  variant: 'inline' | 'card' | 'centered';
  background: 'light' | 'dark' | 'accent';
};

const backgroundMap: Record<NewsletterProps['background'], string> = {
  light: 'bg-neutral-50 text-black',
  dark: 'bg-black text-white',
  accent: 'bg-[#0a0a0a] text-white',
};

const NewsletterRender = ({
  heading,
  subtitle,
  placeholder,
  submitLabel,
  successMessage,
  variant,
  background,
}: NewsletterProps) => {
  const [email, setEmail] = useState('');
  const [status, setStatus] = useState<'idle' | 'loading' | 'success' | 'error'>('idle');

  const isDark = background !== 'light';
  const inputClass = `w-full px-4 py-4 text-sm tracking-wider bg-transparent border ${
    isDark ? 'border-white/20 text-white placeholder-white/40' : 'border-black/20 text-black placeholder-black/40'
  } focus:outline-none focus:border-current transition`;
  const buttonClass = `px-8 py-4 text-xs tracking-[0.25em] uppercase transition ${
    isDark ? 'bg-white text-black hover:bg-white/90' : 'bg-black text-white hover:bg-black/90'
  } disabled:opacity-50`;

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setStatus('loading');
    try {
      const res = await fetch('/api/newsletter/subscribe', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email }),
      });
      if (!res.ok) throw new Error('Failed');
      setStatus('success');
      setEmail('');
    } catch {
      setStatus('error');
    }
  };

  const form = (
    <form onSubmit={onSubmit} className={variant === 'inline' ? 'flex flex-col md:flex-row gap-3' : 'flex flex-col gap-3'}>
      <input
        type="email"
        required
        value={email}
        onChange={(e) => setEmail(e.target.value)}
        placeholder={placeholder}
        className={inputClass}
        disabled={status === 'loading'}
      />
      <button type="submit" className={buttonClass} disabled={status === 'loading'}>
        {status === 'loading' ? '...' : submitLabel}
      </button>
    </form>
  );

  const message = status === 'success' && (
    <p className={`mt-4 text-xs tracking-wider ${isDark ? 'text-white/70' : 'text-black/70'}`}>
      {successMessage}
    </p>
  );

  if (variant === 'card') {
    return (
      <section className={`w-full py-20 ${backgroundMap[background]}`}>
        <div className="max-w-2xl mx-auto px-6">
          <div className={`border ${isDark ? 'border-white/10' : 'border-black/10'} p-10 md:p-16`}>
            <h2 className="font-serif text-3xl md:text-4xl font-light mb-4">{heading}</h2>
            {subtitle && (
              <p className={`text-base mb-8 leading-relaxed ${isDark ? 'text-white/70' : 'text-black/70'}`}>
                {subtitle}
              </p>
            )}
            {form}
            {message}
          </div>
        </div>
      </section>
    );
  }

  if (variant === 'centered') {
    return (
      <section className={`w-full py-24 ${backgroundMap[background]}`}>
        <div className="max-w-xl mx-auto px-6 text-center">
          <h2 className="font-serif text-3xl md:text-5xl font-light mb-4">{heading}</h2>
          {subtitle && (
            <p className={`text-base md:text-lg mb-10 leading-relaxed ${isDark ? 'text-white/70' : 'text-black/70'}`}>
              {subtitle}
            </p>
          )}
          {form}
          {message}
        </div>
      </section>
    );
  }

  return (
    <section className={`w-full py-20 ${backgroundMap[background]}`}>
      <div className="max-w-5xl mx-auto px-6 flex flex-col md:flex-row md:items-center md:justify-between gap-8">
        <div className="md:max-w-md">
          <h2 className="font-serif text-2xl md:text-3xl font-light mb-2">{heading}</h2>
          {subtitle && (
            <p className={`text-sm leading-relaxed ${isDark ? 'text-white/70' : 'text-black/70'}`}>
              {subtitle}
            </p>
          )}
        </div>
        <div className="md:flex-1 md:max-w-md">
          {form}
          {message}
        </div>
      </div>
    </section>
  );
};

export const Newsletter: { config: ComponentConfig<NewsletterProps> } = {
  config: {
    label: 'Newsletter',
    fields: {
      heading: { type: 'text', label: 'Encabezado' },
      subtitle: { type: 'textarea', label: 'Subtítulo' },
      placeholder: { type: 'text', label: 'Placeholder del input' },
      submitLabel: { type: 'text', label: 'Texto del botón' },
      successMessage: { type: 'text', label: 'Mensaje de éxito' },
      variant: {
        type: 'select',
        label: 'Variante',
        options: [
          { label: 'Inline', value: 'inline' },
          { label: 'Tarjeta', value: 'card' },
          { label: 'Centrado', value: 'centered' },
        ],
      },
      background: {
        type: 'select',
        label: 'Fondo',
        options: [
          { label: 'Claro', value: 'light' },
          { label: 'Oscuro', value: 'dark' },
          { label: 'Acento', value: 'accent' },
        ],
      },
    },
    defaultProps: {
      heading: 'Únete a nuestra lista',
      subtitle: 'Recibe las últimas novedades, colecciones y eventos exclusivos directamente en tu email.',
      placeholder: 'tu@email.com',
      submitLabel: 'Suscribirse',
      successMessage: 'Gracias por suscribirte.',
      variant: 'centered',
      background: 'dark',
    },
    render: NewsletterRender,
  },
};

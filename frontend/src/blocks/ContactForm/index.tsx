import type { ComponentConfig } from '@measured/puck';
import { useState } from 'react';
import { Mail, Phone, MapPin, Clock } from 'lucide-react';

export type ContactField = {
  name: string;
  label: string;
  type: 'text' | 'email' | 'tel' | 'textarea' | 'select';
  required: boolean;
  options?: string;
};

export type ContactSideContent = {
  email?: string;
  phone?: string;
  address?: string;
  hours?: string;
};

export type ContactFormProps = {
  heading?: string;
  subtitle?: string;
  fields: ContactField[];
  submitLabel: string;
  successMessage: string;
  action: string;
  layout: 'stacked' | 'split-with-info';
  sideContent?: ContactSideContent;
};

const FormRender = ({
  fields,
  submitLabel,
  successMessage,
  action,
}: {
  fields: ContactField[];
  submitLabel: string;
  successMessage: string;
  action: string;
}) => {
  const [status, setStatus] = useState<'idle' | 'sending' | 'success' | 'error'>('idle');

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setStatus('sending');
    const formData = new FormData(e.currentTarget);
    const data = Object.fromEntries(formData.entries());

    if (action.startsWith('mailto:')) {
      const body = Object.entries(data)
        .map(([k, v]) => `${k}: ${v}`)
        .join('\n');
      window.location.href = `${action}?body=${encodeURIComponent(body)}`;
      setStatus('success');
      return;
    }

    try {
      const res = await fetch('/api/forms/submit', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ formId: action, data }),
      });
      if (!res.ok) throw new Error('Error');
      setStatus('success');
      (e.target as HTMLFormElement).reset();
    } catch {
      setStatus('error');
    }
  };

  if (status === 'success') {
    return (
      <div className="p-8 border border-neutral-200 bg-neutral-50 text-center">
        <p className="font-serif text-xl text-neutral-900">{successMessage}</p>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-5">
      {fields.map((f, i) => {
        const common = {
          name: f.name,
          required: f.required,
          placeholder: f.label + (f.required ? ' *' : ''),
          className:
            'w-full px-0 py-3 bg-transparent border-0 border-b border-neutral-300 focus:border-neutral-900 focus:outline-none focus:ring-0 text-neutral-900 placeholder:text-neutral-400 transition',
        };
        if (f.type === 'textarea') {
          return <textarea key={i} {...common} rows={4} />;
        }
        if (f.type === 'select') {
          const opts = (f.options || '').split(',').map((o) => o.trim()).filter(Boolean);
          return (
            <select key={i} {...common} defaultValue="">
              <option value="" disabled>
                {f.label}
              </option>
              {opts.map((o) => (
                <option key={o} value={o}>
                  {o}
                </option>
              ))}
            </select>
          );
        }
        return <input key={i} type={f.type} {...common} />;
      })}
      <button
        type="submit"
        disabled={status === 'sending'}
        className="inline-block bg-neutral-900 text-white px-8 py-4 text-xs tracking-[0.25em] uppercase hover:bg-neutral-800 transition disabled:opacity-50"
      >
        {status === 'sending' ? 'Enviando…' : submitLabel}
      </button>
      {status === 'error' && (
        <p className="text-sm text-red-600">Ha habido un error. Inténtalo de nuevo.</p>
      )}
    </form>
  );
};

const ContactFormRender = ({
  heading,
  subtitle,
  fields,
  submitLabel,
  successMessage,
  action,
  layout,
  sideContent,
}: ContactFormProps) => {
  return (
    <section className="w-full py-24 px-6 bg-white">
      <div className="max-w-6xl mx-auto">
        {layout === 'split-with-info' ? (
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <div>
              {heading && (
                <h2 className="font-serif text-3xl md:text-5xl font-light mb-4 leading-tight text-neutral-900">
                  {heading}
                </h2>
              )}
              {subtitle && (
                <p className="text-base text-neutral-600 mb-10 leading-relaxed">{subtitle}</p>
              )}
              <div className="space-y-6 text-sm">
                {sideContent?.email && (
                  <div className="flex items-start gap-4">
                    <Mail size={18} className="text-neutral-900 mt-1" />
                    <div>
                      <p className="text-xs tracking-[0.2em] uppercase text-neutral-500 mb-1">Email</p>
                      <p className="text-neutral-900">{sideContent.email}</p>
                    </div>
                  </div>
                )}
                {sideContent?.phone && (
                  <div className="flex items-start gap-4">
                    <Phone size={18} className="text-neutral-900 mt-1" />
                    <div>
                      <p className="text-xs tracking-[0.2em] uppercase text-neutral-500 mb-1">Teléfono</p>
                      <p className="text-neutral-900">{sideContent.phone}</p>
                    </div>
                  </div>
                )}
                {sideContent?.address && (
                  <div className="flex items-start gap-4">
                    <MapPin size={18} className="text-neutral-900 mt-1" />
                    <div>
                      <p className="text-xs tracking-[0.2em] uppercase text-neutral-500 mb-1">Dirección</p>
                      <p className="text-neutral-900 whitespace-pre-line">{sideContent.address}</p>
                    </div>
                  </div>
                )}
                {sideContent?.hours && (
                  <div className="flex items-start gap-4">
                    <Clock size={18} className="text-neutral-900 mt-1" />
                    <div>
                      <p className="text-xs tracking-[0.2em] uppercase text-neutral-500 mb-1">Horario</p>
                      <p className="text-neutral-900 whitespace-pre-line">{sideContent.hours}</p>
                    </div>
                  </div>
                )}
              </div>
            </div>
            <div>
              <FormRender
                fields={fields}
                submitLabel={submitLabel}
                successMessage={successMessage}
                action={action}
              />
            </div>
          </div>
        ) : (
          <div className="max-w-2xl mx-auto">
            {heading && (
              <h2 className="font-serif text-3xl md:text-5xl font-light mb-4 text-center leading-tight text-neutral-900">
                {heading}
              </h2>
            )}
            {subtitle && (
              <p className="text-base text-neutral-600 text-center mb-12 leading-relaxed">
                {subtitle}
              </p>
            )}
            <FormRender
              fields={fields}
              submitLabel={submitLabel}
              successMessage={successMessage}
              action={action}
            />
          </div>
        )}
      </div>
    </section>
  );
};

export const ContactForm: { config: ComponentConfig<ContactFormProps> } = {
  config: {
    label: 'Formulario de contacto',
    fields: {
      heading: { type: 'text', label: 'Título' },
      subtitle: { type: 'textarea', label: 'Subtítulo' },
      fields: {
        type: 'array',
        label: 'Campos del formulario',
        arrayFields: {
          name: { type: 'text', label: 'Nombre interno (name)' },
          label: { type: 'text', label: 'Etiqueta visible' },
          type: {
            type: 'select',
            label: 'Tipo',
            options: [
              { label: 'Texto', value: 'text' },
              { label: 'Email', value: 'email' },
              { label: 'Teléfono', value: 'tel' },
              { label: 'Área de texto', value: 'textarea' },
              { label: 'Selector', value: 'select' },
            ],
          },
          required: {
            type: 'radio',
            label: 'Requerido',
            options: [
              { label: 'No', value: false },
              { label: 'Sí', value: true },
            ],
          },
          options: { type: 'text', label: 'Opciones (separadas por coma, solo select)' },
        },
      },
      submitLabel: { type: 'text', label: 'Texto del botón' },
      successMessage: { type: 'textarea', label: 'Mensaje de éxito' },
      action: { type: 'text', label: 'Acción (mailto:… o form-id)' },
      layout: {
        type: 'radio',
        label: 'Layout',
        options: [
          { label: 'Apilado', value: 'stacked' },
          { label: 'Dividido con info', value: 'split-with-info' },
        ],
      },
      sideContent: {
        type: 'object',
        label: 'Información lateral (solo split)',
        objectFields: {
          email: { type: 'text', label: 'Email' },
          phone: { type: 'text', label: 'Teléfono' },
          address: { type: 'textarea', label: 'Dirección' },
          hours: { type: 'textarea', label: 'Horario' },
        },
      },
    },
    defaultProps: {
      heading: 'Hablemos',
      subtitle: 'Cuéntanos tu proyecto y te responderemos en menos de 24h.',
      submitLabel: 'Enviar mensaje',
      successMessage: 'Gracias. Nos pondremos en contacto contigo muy pronto.',
      action: 'contact',
      layout: 'split-with-info',
      sideContent: {
        email: 'hola@hawkins.com',
        phone: '+34 912 345 678',
        address: 'Calle Serrano 45\n28001 Madrid, España',
        hours: 'Lun – Vie · 9:00 – 19:00',
      },
      fields: [
        { name: 'name', label: 'Nombre', type: 'text', required: true },
        { name: 'email', label: 'Email', type: 'email', required: true },
        { name: 'phone', label: 'Teléfono', type: 'tel', required: false },
        { name: 'message', label: 'Cuéntanos tu proyecto', type: 'textarea', required: true },
      ],
    },
    render: ContactFormRender,
  },
};

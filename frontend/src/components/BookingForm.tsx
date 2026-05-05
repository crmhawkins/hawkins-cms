'use client';

import { useEffect, useMemo, useState } from 'react';

export interface BookingFormService {
  id: string;
  name: string;
  description?: string;
  duration_minutes: number;
  price?: number;
  image_url?: string | null;
}

export interface BookingFormProps {
  services: BookingFormService[];
  defaultServiceId?: string;
  advanceDays?: number;
  compact?: boolean;
  onSuccess?: (token: string) => void;
}

function formatSpanishDate(date: Date): string {
  return new Intl.DateTimeFormat('es-ES', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
  }).format(date);
}

function toISODate(d: Date): string {
  const y = d.getFullYear();
  const m = (d.getMonth() + 1).toString().padStart(2, '0');
  const dd = d.getDate().toString().padStart(2, '0');
  return `${y}-${m}-${dd}`;
}

export function BookingForm({
  services,
  defaultServiceId,
  advanceDays = 30,
  compact = false,
  onSuccess,
}: BookingFormProps) {
  const initialService =
    defaultServiceId && services.find((s) => s.id === defaultServiceId)
      ? defaultServiceId
      : services[0]?.id || '';

  const [serviceId, setServiceId] = useState<string>(initialService);
  const [date, setDate] = useState<string>('');
  const [time, setTime] = useState<string>('');
  const [slots, setSlots] = useState<string[]>([]);
  const [loadingSlots, setLoadingSlots] = useState(false);
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [notes, setNotes] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const today = useMemo(() => {
    const t = new Date();
    t.setHours(0, 0, 0, 0);
    return t;
  }, []);
  const minDate = toISODate(today);
  const maxDateObj = useMemo(() => {
    const m = new Date(today);
    m.setDate(m.getDate() + advanceDays);
    return m;
  }, [today, advanceDays]);
  const maxDate = toISODate(maxDateObj);

  useEffect(() => {
    if (!serviceId || !date) {
      setSlots([]);
      return;
    }
    let cancelled = false;
    setLoadingSlots(true);
    setTime('');
    fetch(`/api/booking/availability?service=${encodeURIComponent(serviceId)}&date=${date}`)
      .then((r) => r.json())
      .then((data) => {
        if (cancelled) return;
        setSlots(Array.isArray(data.slots) ? data.slots : []);
      })
      .catch(() => !cancelled && setSlots([]))
      .finally(() => !cancelled && setLoadingSlots(false));
    return () => {
      cancelled = true;
    };
  }, [serviceId, date]);

  const onSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    if (!serviceId || !date || !time || !name || !email) {
      setError('Completa todos los campos obligatorios.');
      return;
    }
    setSubmitting(true);
    try {
      const res = await fetch('/api/booking/create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          service_id: serviceId,
          customer_name: name,
          customer_email: email,
          customer_phone: phone || undefined,
          date,
          time,
          notes: notes || undefined,
        }),
      });
      const data = await res.json();
      if (!res.ok || !data.ok) {
        setError(data.error || 'No se pudo crear la reserva.');
        setSubmitting(false);
        return;
      }
      if (onSuccess) {
        onSuccess(data.token);
      } else {
        window.location.href = `/reservar/confirmacion?token=${encodeURIComponent(data.token)}`;
      }
    } catch (err: any) {
      setError(err.message || 'Error inesperado');
      setSubmitting(false);
    }
  };

  const inputClass =
    'w-full px-4 py-3 text-sm bg-transparent border border-black/20 text-black placeholder-black/40 focus:outline-none focus:border-black transition';
  const labelClass = 'block text-[10px] tracking-[0.25em] uppercase text-black/60 mb-2';

  const selectedDateObj = date ? new Date(`${date}T00:00:00`) : null;

  return (
    <form onSubmit={onSubmit} className="w-full space-y-8">
      {services.length > 1 && (
        <div>
          <label className={labelClass}>Servicio</label>
          <select
            className={inputClass}
            value={serviceId}
            onChange={(e) => setServiceId(e.target.value)}
            required
          >
            {services.map((s) => (
              <option key={s.id} value={s.id}>
                {s.name}
                {typeof s.price === 'number' ? ` — ${s.price} €` : ''}
                {s.duration_minutes ? ` · ${s.duration_minutes} min` : ''}
              </option>
            ))}
          </select>
        </div>
      )}

      <div className={compact ? '' : 'grid md:grid-cols-2 gap-6'}>
        <div>
          <label className={labelClass}>Fecha</label>
          <input
            type="date"
            className={inputClass}
            value={date}
            min={minDate}
            max={maxDate}
            onChange={(e) => setDate(e.target.value)}
            required
          />
          {selectedDateObj && (
            <p className="mt-2 text-xs text-black/60 capitalize">
              {formatSpanishDate(selectedDateObj)}
            </p>
          )}
        </div>

        <div>
          <label className={labelClass}>Horario</label>
          {!date ? (
            <p className="text-xs text-black/50 py-3">Selecciona una fecha primero.</p>
          ) : loadingSlots ? (
            <p className="text-xs text-black/50 py-3">Cargando horarios…</p>
          ) : slots.length === 0 ? (
            <p className="text-xs text-black/60 py-3">No hay horarios libres este día.</p>
          ) : (
            <div className="grid grid-cols-3 sm:grid-cols-4 gap-2">
              {slots.map((s) => (
                <button
                  type="button"
                  key={s}
                  onClick={() => setTime(s)}
                  className={`py-2 text-xs tracking-wider border transition ${
                    time === s
                      ? 'bg-black text-white border-black'
                      : 'border-black/20 hover:border-black'
                  }`}
                >
                  {s}
                </button>
              ))}
            </div>
          )}
        </div>
      </div>

      <div className="grid md:grid-cols-2 gap-6">
        <div>
          <label className={labelClass}>Nombre</label>
          <input
            type="text"
            className={inputClass}
            value={name}
            onChange={(e) => setName(e.target.value)}
            required
          />
        </div>
        <div>
          <label className={labelClass}>Email</label>
          <input
            type="email"
            className={inputClass}
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />
        </div>
        <div className="md:col-span-2">
          <label className={labelClass}>Teléfono</label>
          <input
            type="tel"
            className={inputClass}
            value={phone}
            onChange={(e) => setPhone(e.target.value)}
          />
        </div>
        <div className="md:col-span-2">
          <label className={labelClass}>Notas</label>
          <textarea
            className={inputClass}
            rows={3}
            value={notes}
            onChange={(e) => setNotes(e.target.value)}
          />
        </div>
      </div>

      {error && (
        <p className="text-xs text-red-600 tracking-wider">{error}</p>
      )}

      <button
        type="submit"
        disabled={submitting || !time}
        className="px-8 py-4 text-xs tracking-[0.25em] uppercase bg-black text-white hover:bg-black/90 transition disabled:opacity-40"
      >
        {submitting ? 'Reservando…' : 'Confirmar reserva'}
      </button>
    </form>
  );
}

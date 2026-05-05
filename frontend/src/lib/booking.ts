/**
 * Booking: servicios, disponibilidad, reservas.
 * Fuente de verdad: Directus (colecciones booking_services, bookings).
 */
import { cms } from './cms';
import { randomBytes } from 'crypto';

const DIRECTUS = process.env.DIRECTUS_INTERNAL_URL || 'http://directus:8055';
const TOKEN = process.env.DIRECTUS_STATIC_TOKEN || '';

export interface BookingService {
  id: string;
  name: string;
  description?: string;
  duration_minutes: number;
  price?: number;
  image?: string;
  active: boolean;
}

export type BookingStatus = 'pending' | 'confirmed' | 'cancelled' | 'completed';

export interface Booking {
  id: string;
  service_id: string;
  customer_name: string;
  customer_email: string;
  customer_phone?: string;
  date: string; // YYYY-MM-DD
  time: string; // HH:MM
  duration_minutes: number;
  notes?: string;
  booking_status: BookingStatus;
  confirmation_token: string;
  booking_number?: string;
  date_created?: string;
}

export interface BookingSettings {
  booking_duration_minutes: number;
  booking_buffer_minutes: number;
  booking_advance_days: number;
  booking_hours_start: string; // "09:00"
  booking_hours_end: string; // "18:00"
  booking_days: string[]; // ['mon','tue',...]
}

const DAY_KEYS = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];

async function dx<T = any>(path: string, init: RequestInit = {}): Promise<T> {
  const res = await fetch(`${DIRECTUS}${path}`, {
    ...init,
    headers: {
      ...(init.headers || {}),
      ...(TOKEN ? { Authorization: `Bearer ${TOKEN}` } : {}),
      'Content-Type': 'application/json',
    },
    cache: 'no-store',
  });
  if (!res.ok) {
    const body = await res.text().catch(() => '');
    throw new Error(`Directus ${path}: ${res.status} ${body}`);
  }
  const json = await res.json();
  return json.data as T;
}

export async function getBookingSettings(): Promise<BookingSettings> {
  const s = (await cms.getSettings()) as any;
  return {
    booking_duration_minutes: s?.booking_duration_minutes ?? 60,
    booking_buffer_minutes: s?.booking_buffer_minutes ?? 0,
    booking_advance_days: s?.booking_advance_days ?? 30,
    booking_hours_start: s?.booking_hours_start ?? '09:00',
    booking_hours_end: s?.booking_hours_end ?? '18:00',
    booking_days: s?.booking_days ?? ['mon', 'tue', 'wed', 'thu', 'fri'],
  };
}

export async function listServices(): Promise<BookingService[]> {
  try {
    return await dx<BookingService[]>(
      '/items/booking_services?filter[active][_eq]=true&sort=name&limit=-1',
    );
  } catch {
    return [];
  }
}

export async function getService(id: string): Promise<BookingService | null> {
  try {
    return await dx<BookingService>(`/items/booking_services/${id}`);
  } catch {
    return null;
  }
}

function toMinutes(t: string): number {
  const [h, m] = t.split(':').map((x) => parseInt(x, 10));
  return h * 60 + m;
}

function toHHMM(m: number): string {
  const h = Math.floor(m / 60);
  const mm = m % 60;
  return `${h.toString().padStart(2, '0')}:${mm.toString().padStart(2, '0')}`;
}

export async function getAvailability(
  serviceId: string,
  date: string,
): Promise<string[]> {
  const service = await getService(serviceId);
  if (!service || !service.active) return [];

  const settings = await getBookingSettings();
  const duration = service.duration_minutes || settings.booking_duration_minutes;
  const buffer = settings.booking_buffer_minutes || 0;

  // Comprueba que el día de la semana esté habilitado
  const d = new Date(`${date}T00:00:00`);
  const dayKey = DAY_KEYS[d.getDay()];
  if (!settings.booking_days.includes(dayKey)) return [];

  // No permitir fechas en el pasado
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  if (d < today) return [];

  // No permitir más allá de advance_days
  const maxDate = new Date(today);
  maxDate.setDate(maxDate.getDate() + (settings.booking_advance_days || 30));
  if (d > maxDate) return [];

  const start = toMinutes(settings.booking_hours_start);
  const end = toMinutes(settings.booking_hours_end);
  const step = duration + buffer;

  // Slots candidatos
  const candidates: string[] = [];
  for (let t = start; t + duration <= end; t += step) {
    candidates.push(toHHMM(t));
  }

  // Filtrar por reservas existentes
  let existing: Array<{ time: string; duration_minutes: number }> = [];
  try {
    existing = await dx<Array<{ time: string; duration_minutes: number }>>(
      `/items/bookings?filter[date][_eq]=${date}&filter[booking_status][_neq]=cancelled&fields=time,duration_minutes&limit=-1`,
    );
  } catch {
    existing = [];
  }

  const taken = new Set<string>();
  for (const b of existing) {
    const bStart = toMinutes(b.time);
    const bEnd = bStart + (b.duration_minutes || duration) + buffer;
    for (const c of candidates) {
      const cStart = toMinutes(c);
      const cEnd = cStart + duration;
      // solapamiento
      if (cStart < bEnd && cEnd > bStart) taken.add(c);
    }
  }

  // Si es hoy, descartar horas ya pasadas
  const now = new Date();
  const isToday = d.toDateString() === now.toDateString();
  const nowMin = now.getHours() * 60 + now.getMinutes();

  return candidates.filter((c) => !taken.has(c) && (!isToday || toMinutes(c) > nowMin));
}

function generateBookingNumber(date: string): string {
  const compact = date.replace(/-/g, '');
  const rand = randomBytes(2).toString('hex').toUpperCase();
  return `BK-${compact}-${rand}`;
}

export interface CreateBookingInput {
  service_id: string;
  customer_name: string;
  customer_email: string;
  customer_phone?: string;
  date: string;
  time: string;
  notes?: string;
}

export async function createBooking(
  input: CreateBookingInput,
): Promise<{ ok: boolean; token?: string; bookingNumber?: string; error?: string }> {
  const service = await getService(input.service_id);
  if (!service || !service.active) {
    return { ok: false, error: 'Servicio no disponible' };
  }

  // Double-check disponibilidad
  const slots = await getAvailability(input.service_id, input.date);
  if (!slots.includes(input.time)) {
    return { ok: false, error: 'Ese horario ya no está disponible' };
  }

  const token = randomBytes(24).toString('hex');
  const bookingNumber = generateBookingNumber(input.date);

  try {
    await dx('/items/bookings', {
      method: 'POST',
      body: JSON.stringify({
        service_id: input.service_id,
        customer_name: input.customer_name,
        customer_email: input.customer_email,
        customer_phone: input.customer_phone || null,
        date: input.date,
        time: input.time,
        duration_minutes: service.duration_minutes,
        notes: input.notes || null,
        booking_status: 'pending',
        confirmation_token: token,
        booking_number: bookingNumber,
      }),
    });
    // TODO: send email (cliente + admin) con enlaces de confirmar/cancelar
    return { ok: true, token, bookingNumber };
  } catch (e: any) {
    return { ok: false, error: e.message };
  }
}

export async function getBookingByToken(
  token: string,
): Promise<(Booking & { service_name?: string }) | null> {
  try {
    const list = await dx<Booking[]>(
      `/items/bookings?filter[confirmation_token][_eq]=${encodeURIComponent(token)}&limit=1`,
    );
    const b = list[0];
    if (!b) return null;
    const service = await getService(b.service_id);
    return { ...b, service_name: service?.name };
  } catch {
    return null;
  }
}

export async function confirmBooking(token: string): Promise<boolean> {
  const b = await getBookingByToken(token);
  if (!b) return false;
  try {
    await dx(`/items/bookings/${b.id}`, {
      method: 'PATCH',
      body: JSON.stringify({ booking_status: 'confirmed' }),
    });
    // TODO: send email de confirmación
    return true;
  } catch {
    return false;
  }
}

export async function cancelBooking(token: string): Promise<boolean> {
  const b = await getBookingByToken(token);
  if (!b) return false;
  try {
    await dx(`/items/bookings/${b.id}`, {
      method: 'PATCH',
      body: JSON.stringify({ booking_status: 'cancelled' }),
    });
    // TODO: send email notificando cancelación
    return true;
  } catch {
    return false;
  }
}

import Link from 'next/link';
import { requireModule } from '@/lib/modules';
import { getBookingByToken } from '@/lib/booking';

export const dynamic = 'force-dynamic';

function formatDate(iso: string): string {
  try {
    const d = new Date(`${iso}T00:00:00`);
    return new Intl.DateTimeFormat('es-ES', {
      weekday: 'long',
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    }).format(d);
  } catch {
    return iso;
  }
}

export default async function ConfirmacionPage({
  searchParams,
}: {
  searchParams?: Promise<{ token?: string }>;
}) {
  await requireModule('booking');
  const sp = searchParams ? await searchParams : {};
  const token = sp.token || '';
  const booking = token ? await getBookingByToken(token) : null;

  return (
    <main className="min-h-screen bg-[#fdfcf7] text-black">
      <div className="max-w-2xl mx-auto px-6 py-24 text-center">
        <p className="text-[10px] tracking-[0.3em] uppercase text-black/50 mb-4">Reserva registrada</p>
        <h1 className="font-serif text-4xl md:text-5xl font-light mb-6">Gracias</h1>
        <p className="text-black/70 mb-10">
          Tu solicitud de reserva se ha registrado. Revisa tu email para los detalles y la confirmación final.
        </p>

        {booking ? (
          <div className="border border-black/10 p-8 text-left space-y-3">
            {booking.booking_number && (
              <div className="flex justify-between text-sm">
                <span className="text-black/50">Nº de reserva</span>
                <span className="font-mono">{booking.booking_number}</span>
              </div>
            )}
            <div className="flex justify-between text-sm">
              <span className="text-black/50">Servicio</span>
              <span>{booking.service_name || '—'}</span>
            </div>
            <div className="flex justify-between text-sm">
              <span className="text-black/50">Fecha</span>
              <span className="capitalize">{formatDate(booking.date)}</span>
            </div>
            <div className="flex justify-between text-sm">
              <span className="text-black/50">Hora</span>
              <span>{booking.time}</span>
            </div>
            <div className="flex justify-between text-sm">
              <span className="text-black/50">A nombre de</span>
              <span>{booking.customer_name}</span>
            </div>
            <div className="flex justify-between text-sm">
              <span className="text-black/50">Email</span>
              <span>{booking.customer_email}</span>
            </div>
            <div className="flex justify-between text-sm">
              <span className="text-black/50">Estado</span>
              <span className="uppercase tracking-wider text-xs">{booking.booking_status}</span>
            </div>
          </div>
        ) : (
          <p className="text-sm text-black/50">No se encontró la reserva.</p>
        )}

        <div className="mt-10">
          <Link
            href="/"
            className="inline-block px-8 py-4 text-xs tracking-[0.25em] uppercase border border-black hover:bg-black hover:text-white transition"
          >
            Volver al inicio
          </Link>
        </div>
      </div>
    </main>
  );
}

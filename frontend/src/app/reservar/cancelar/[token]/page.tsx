import Link from 'next/link';
import { requireModule } from '@/lib/modules';
import { cancelBooking, getBookingByToken } from '@/lib/booking';

export const dynamic = 'force-dynamic';

export default async function CancelarPage({
  params,
}: {
  params: Promise<{ token: string }>;
}) {
  await requireModule('booking');
  const { token } = await params;

  const before = await getBookingByToken(token);
  let ok = false;
  let alreadyCancelled = false;

  if (before) {
    if (before.booking_status === 'cancelled') {
      alreadyCancelled = true;
      ok = true;
    } else {
      ok = await cancelBooking(token);
    }
  }

  return (
    <main className="min-h-screen bg-[#fdfcf7] text-black">
      <div className="max-w-2xl mx-auto px-6 py-24 text-center">
        {ok ? (
          <>
            <p className="text-[10px] tracking-[0.3em] uppercase text-black/50 mb-4">
              {alreadyCancelled ? 'Ya estaba cancelada' : 'Cancelación'}
            </p>
            <h1 className="font-serif text-4xl md:text-5xl font-light mb-6">Reserva cancelada</h1>
            <p className="text-black/70">
              Tu reserva ha sido cancelada. Si fue un error, puedes crear una nueva desde la página de reservas.
            </p>
          </>
        ) : (
          <>
            <p className="text-[10px] tracking-[0.3em] uppercase text-black/50 mb-4">Error</p>
            <h1 className="font-serif text-4xl md:text-5xl font-light mb-6">No se pudo cancelar</h1>
            <p className="text-black/70">
              El enlace no es válido o la reserva ya no existe.
            </p>
          </>
        )}

        <div className="mt-10 flex gap-4 justify-center">
          <Link
            href="/reservar"
            className="inline-block px-8 py-4 text-xs tracking-[0.25em] uppercase bg-black text-white hover:bg-black/90 transition"
          >
            Nueva reserva
          </Link>
          <Link
            href="/"
            className="inline-block px-8 py-4 text-xs tracking-[0.25em] uppercase border border-black hover:bg-black hover:text-white transition"
          >
            Inicio
          </Link>
        </div>
      </div>
    </main>
  );
}

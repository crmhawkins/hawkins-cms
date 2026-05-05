import { requireModule } from '@/lib/modules';
import { listServices, getBookingSettings } from '@/lib/booking';
import { cms } from '@/lib/cms';
import { BookingForm } from '@/components/BookingForm';

export const dynamic = 'force-dynamic';

export default async function ReservarPage({
  searchParams,
}: {
  searchParams?: Promise<{ service?: string }>;
}) {
  await requireModule('booking');

  const sp = searchParams ? await searchParams : {};
  const services = await listServices();
  const settings = await getBookingSettings();

  const formServices = services.map((s) => ({
    id: s.id,
    name: s.name,
    description: s.description,
    duration_minutes: s.duration_minutes,
    price: s.price,
    image_url: cms.mediaUrl(s.image) || null,
  }));

  if (formServices.length === 0) {
    return (
      <main className="min-h-screen bg-[#fdfcf7] text-black">
        <div className="max-w-3xl mx-auto px-6 py-24 text-center">
          <h1 className="font-serif text-4xl md:text-5xl font-light mb-4">Reservas</h1>
          <p className="text-black/60">No hay servicios disponibles en este momento.</p>
        </div>
      </main>
    );
  }

  return (
    <main className="min-h-screen bg-[#fdfcf7] text-black">
      <div className="max-w-3xl mx-auto px-6 py-20 md:py-28">
        <header className="mb-12 text-center">
          <p className="text-[10px] tracking-[0.3em] uppercase text-black/50 mb-4">Reservas</p>
          <h1 className="font-serif text-4xl md:text-5xl font-light">Reserva tu cita</h1>
          <p className="mt-4 text-black/60 text-sm md:text-base max-w-xl mx-auto">
            Elige servicio, fecha y horario. Recibirás la confirmación por email.
          </p>
        </header>

        <BookingForm
          services={formServices}
          defaultServiceId={sp.service}
          advanceDays={settings.booking_advance_days}
        />
      </div>
    </main>
  );
}

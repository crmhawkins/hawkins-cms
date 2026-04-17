import { NextRequest, NextResponse } from 'next/server';
import { isModuleEnabled } from '@/lib/modules';
import { createBooking } from '@/lib/booking';

export async function POST(req: NextRequest) {
  if (!(await isModuleEnabled('booking'))) {
    return NextResponse.json({ error: 'Module disabled' }, { status: 404 });
  }
  try {
    const body = await req.json();
    const { service_id, customer_name, customer_email, customer_phone, date, time, notes } = body || {};

    if (!service_id || !customer_name || !customer_email || !date || !time) {
      return NextResponse.json({ error: 'Faltan campos obligatorios' }, { status: 400 });
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(customer_email)) {
      return NextResponse.json({ error: 'Email inválido' }, { status: 400 });
    }
    if (!/^\d{4}-\d{2}-\d{2}$/.test(date)) {
      return NextResponse.json({ error: 'Fecha inválida' }, { status: 400 });
    }
    if (!/^\d{2}:\d{2}$/.test(time)) {
      return NextResponse.json({ error: 'Hora inválida' }, { status: 400 });
    }

    const r = await createBooking({
      service_id,
      customer_name: String(customer_name).trim(),
      customer_email: String(customer_email).trim().toLowerCase(),
      customer_phone: customer_phone ? String(customer_phone).trim() : undefined,
      date,
      time,
      notes: notes ? String(notes).trim() : undefined,
    });

    if (!r.ok) {
      return NextResponse.json({ error: r.error || 'No se pudo crear la reserva' }, { status: 400 });
    }
    return NextResponse.json({ ok: true, token: r.token, bookingNumber: r.bookingNumber });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}

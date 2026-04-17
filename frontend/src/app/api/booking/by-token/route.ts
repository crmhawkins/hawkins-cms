import { NextRequest, NextResponse } from 'next/server';
import { isModuleEnabled } from '@/lib/modules';
import { getBookingByToken } from '@/lib/booking';

export async function GET(req: NextRequest) {
  if (!(await isModuleEnabled('booking'))) {
    return NextResponse.json({ error: 'Module disabled' }, { status: 404 });
  }
  const { searchParams } = new URL(req.url);
  const token = searchParams.get('token');
  if (!token) return NextResponse.json({ error: 'token requerido' }, { status: 400 });
  const b = await getBookingByToken(token);
  if (!b) return NextResponse.json({ error: 'No encontrada' }, { status: 404 });
  return NextResponse.json({
    booking_number: b.booking_number,
    service_name: b.service_name,
    date: b.date,
    time: b.time,
    customer_name: b.customer_name,
    customer_email: b.customer_email,
    status: b.booking_status,
  });
}

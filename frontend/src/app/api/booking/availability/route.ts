import { NextRequest, NextResponse } from 'next/server';
import { isModuleEnabled } from '@/lib/modules';
import { getAvailability } from '@/lib/booking';

export async function GET(req: NextRequest) {
  if (!(await isModuleEnabled('booking'))) {
    return NextResponse.json({ error: 'Module disabled' }, { status: 404 });
  }
  const { searchParams } = new URL(req.url);
  const service = searchParams.get('service');
  const date = searchParams.get('date');
  if (!service || !date) {
    return NextResponse.json({ error: 'service y date requeridos' }, { status: 400 });
  }
  if (!/^\d{4}-\d{2}-\d{2}$/.test(date)) {
    return NextResponse.json({ error: 'date inválida (YYYY-MM-DD)' }, { status: 400 });
  }
  const slots = await getAvailability(service, date);
  return NextResponse.json({ slots });
}

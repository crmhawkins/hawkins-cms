import { NextRequest, NextResponse } from 'next/server';
import { isModuleEnabled } from '@/lib/modules';
import { cancelBooking } from '@/lib/booking';

export async function POST(req: NextRequest) {
  if (!(await isModuleEnabled('booking'))) {
    return NextResponse.json({ error: 'Module disabled' }, { status: 404 });
  }
  try {
    const { token } = await req.json();
    if (!token) return NextResponse.json({ error: 'token requerido' }, { status: 400 });
    const ok = await cancelBooking(String(token));
    if (!ok) return NextResponse.json({ error: 'No se pudo cancelar' }, { status: 400 });
    return NextResponse.json({ ok: true });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}

import { NextRequest, NextResponse } from 'next/server';
import { subscribe } from '@/lib/newsletter';
import { isModuleEnabled } from '@/lib/modules';

export async function POST(req: NextRequest) {
  if (!(await isModuleEnabled('newsletter'))) {
    return NextResponse.json({ error: 'Module disabled' }, { status: 404 });
  }

  try {
    const { email, name, source, tags } = await req.json();
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      return NextResponse.json({ error: 'Email inválido' }, { status: 400 });
    }
    const r = await subscribe({ email, name, source, tags });
    if (!r.ok) return NextResponse.json({ error: r.error }, { status: 500 });
    return NextResponse.json({ ok: true });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}

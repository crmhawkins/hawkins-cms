import { NextResponse } from 'next/server';
import { isModuleEnabled } from '@/lib/modules';
import { listServices } from '@/lib/booking';
import { cms } from '@/lib/cms';

export async function GET() {
  if (!(await isModuleEnabled('booking'))) {
    return NextResponse.json({ error: 'Module disabled' }, { status: 404 });
  }
  const services = await listServices();
  const out = services.map((s) => ({
    id: s.id,
    name: s.name,
    description: s.description,
    duration_minutes: s.duration_minutes,
    price: s.price,
    image_url: cms.mediaUrl(s.image) || null,
    active: s.active,
  }));
  return NextResponse.json({ services: out });
}

import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';
import { requireModule } from '@/lib/modules';
import { getCurrentMember } from '@/lib/auth-members';

export async function GET(req: NextRequest) {
  await requireModule('members');
  const m = await getCurrentMember(req);
  if (!m) {
    return NextResponse.json({ ok: false, error: 'No autenticado' }, { status: 401 });
  }
  return NextResponse.json({
    ok: true,
    member: {
      id: m.id,
      email: m.email,
      name: m.name,
      tier: m.tier,
      avatar: m.avatar || null,
    },
  });
}

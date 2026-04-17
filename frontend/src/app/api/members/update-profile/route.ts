import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';
import { z } from 'zod';
import { requireModule } from '@/lib/modules';
import {
  getCurrentMember,
  updateMember,
  verifyPassword,
  hashPassword,
} from '@/lib/auth-members';

const schema = z.object({
  name: z.string().min(1).max(120).optional(),
  avatar: z.string().max(500).nullable().optional(),
  current_password: z.string().optional(),
  new_password: z.string().min(8).optional(),
});

export async function PATCH(req: NextRequest) {
  await requireModule('members');
  const member = await getCurrentMember(req);
  if (!member) {
    return NextResponse.json({ ok: false, error: 'No autenticado' }, { status: 401 });
  }
  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return NextResponse.json({ ok: false, error: 'JSON inválido' }, { status: 400 });
  }
  const parsed = schema.safeParse(body);
  if (!parsed.success) {
    return NextResponse.json(
      { ok: false, error: parsed.error.issues[0]?.message || 'Datos inválidos' },
      { status: 400 }
    );
  }
  const data = parsed.data;
  const patch: Record<string, any> = {};
  if (typeof data.name === 'string') patch.name = data.name;
  if (data.avatar !== undefined) patch.avatar = data.avatar;

  if (data.new_password) {
    if (!data.current_password) {
      return NextResponse.json(
        { ok: false, error: 'Debes indicar la contraseña actual' },
        { status: 400 }
      );
    }
    const ok = await verifyPassword(data.current_password, member.password_hash || '');
    if (!ok) {
      return NextResponse.json(
        { ok: false, error: 'Contraseña actual incorrecta' },
        { status: 400 }
      );
    }
    patch.password_hash = await hashPassword(data.new_password);
  }

  if (Object.keys(patch).length === 0) {
    return NextResponse.json({ ok: true });
  }

  try {
    await updateMember(member.id, patch);
  } catch {
    return NextResponse.json(
      { ok: false, error: 'No se pudo actualizar' },
      { status: 500 }
    );
  }
  return NextResponse.json({ ok: true });
}

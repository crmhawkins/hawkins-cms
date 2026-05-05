import { NextResponse } from 'next/server';
import { z } from 'zod';
import { requireModule } from '@/lib/modules';
import { findByResetToken, hashPassword, updateMember } from '@/lib/auth-members';

const schema = z.object({
  token: z.string().min(8),
  password: z.string().min(8, 'La contraseña debe tener al menos 8 caracteres'),
});

export async function POST(req: Request) {
  await requireModule('members');
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
  const { token, password } = parsed.data;
  const member = await findByResetToken(token);
  if (!member) {
    return NextResponse.json(
      { ok: false, error: 'Token inválido o expirado' },
      { status: 400 }
    );
  }
  if (member.reset_expires) {
    const exp = new Date(member.reset_expires).getTime();
    if (Number.isFinite(exp) && exp < Date.now()) {
      return NextResponse.json(
        { ok: false, error: 'Token inválido o expirado' },
        { status: 400 }
      );
    }
  }
  const password_hash = await hashPassword(password);
  try {
    await updateMember(member.id, {
      password_hash,
      reset_token: null,
      reset_expires: null,
    });
  } catch {
    return NextResponse.json(
      { ok: false, error: 'No se pudo actualizar la contraseña' },
      { status: 500 }
    );
  }
  return NextResponse.json({ ok: true });
}

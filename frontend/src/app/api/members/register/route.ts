import { NextResponse } from 'next/server';
import { z } from 'zod';
import { requireModule } from '@/lib/modules';
import {
  createMember,
  findByEmail,
  createToken,
  setCookie,
} from '@/lib/auth-members';

const schema = z.object({
  email: z.string().email(),
  password: z.string().min(8, 'La contraseña debe tener al menos 8 caracteres'),
  name: z.string().min(1).max(120),
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
  const { email, password, name } = parsed.data;

  const existing = await findByEmail(email);
  if (existing) {
    return NextResponse.json(
      { ok: false, error: 'Ese email ya está registrado' },
      { status: 409 }
    );
  }

  let member;
  try {
    member = await createMember({ email, password, name });
  } catch {
    return NextResponse.json(
      { ok: false, error: 'No se pudo crear la cuenta' },
      { status: 500 }
    );
  }

  // TODO: enviar email de verificación con verification_token
  const token = await createToken(member.id);
  const res = NextResponse.json({ ok: true });
  return setCookie(res, token);
}

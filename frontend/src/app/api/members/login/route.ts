import { NextResponse } from 'next/server';
import { z } from 'zod';
import { requireModule } from '@/lib/modules';
import {
  findByEmail,
  verifyPassword,
  createToken,
  setCookie,
  touchLastLogin,
} from '@/lib/auth-members';

const schema = z.object({
  email: z.string().email(),
  password: z.string().min(1),
});

const GENERIC = { ok: false, error: 'Credenciales incorrectas' } as const;

export async function POST(req: Request) {
  await requireModule('members');
  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return NextResponse.json(GENERIC, { status: 401 });
  }
  const parsed = schema.safeParse(body);
  if (!parsed.success) {
    return NextResponse.json(GENERIC, { status: 401 });
  }
  const { email, password } = parsed.data;
  const member = await findByEmail(email);
  if (!member || !member.password_hash) {
    return NextResponse.json(GENERIC, { status: 401 });
  }
  const ok = await verifyPassword(password, member.password_hash);
  if (!ok) {
    return NextResponse.json(GENERIC, { status: 401 });
  }
  await touchLastLogin(member.id);
  const token = await createToken(member.id);
  const res = NextResponse.json({ ok: true });
  return setCookie(res, token);
}

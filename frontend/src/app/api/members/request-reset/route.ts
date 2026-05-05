import { NextResponse } from 'next/server';
import { z } from 'zod';
import { requireModule } from '@/lib/modules';
import { findByEmail, updateMember } from '@/lib/auth-members';

const schema = z.object({
  email: z.string().email(),
});

export async function POST(req: Request) {
  await requireModule('members');
  let body: unknown;
  try {
    body = await req.json();
  } catch {
    return NextResponse.json({ ok: true });
  }
  const parsed = schema.safeParse(body);
  // Nunca revelamos si el email existe
  if (!parsed.success) return NextResponse.json({ ok: true });

  const member = await findByEmail(parsed.data.email);
  if (member) {
    const reset_token = crypto.randomUUID();
    const reset_expires = new Date(Date.now() + 60 * 60 * 1000).toISOString();
    try {
      await updateMember(member.id, { reset_token, reset_expires });
      // TODO: enviar email con link /miembros/reset/{reset_token}
    } catch {
      /* swallow */
    }
  }
  return NextResponse.json({ ok: true });
}

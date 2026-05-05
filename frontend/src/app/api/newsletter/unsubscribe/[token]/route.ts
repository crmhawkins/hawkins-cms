import { NextRequest, NextResponse } from 'next/server';
import { unsubscribeByToken } from '@/lib/newsletter';

export async function GET(
  _req: NextRequest,
  { params }: { params: Promise<{ token: string }> }
) {
  const { token } = await params;
  const ok = await unsubscribeByToken(token);
  if (!ok) {
    return NextResponse.json({ error: 'Token no válido' }, { status: 404 });
  }
  // Redirigir a página amigable
  return NextResponse.redirect(new URL('/newsletter/bye', process.env.PUBLIC_URL || 'http://localhost:3000'));
}

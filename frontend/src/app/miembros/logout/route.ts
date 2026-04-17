import { NextResponse } from 'next/server';
import { clearCookie } from '@/lib/auth-members';

export async function GET(req: Request) {
  const url = new URL(req.url);
  const res = NextResponse.redirect(new URL('/', url.origin));
  return clearCookie(res);
}

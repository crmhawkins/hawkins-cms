/**
 * Sistema de autenticación para el módulo MEMBERS.
 * Usa JWT (jose) + cookie httpOnly + bcryptjs para passwords.
 */
import bcrypt from 'bcryptjs';
import { SignJWT, jwtVerify } from 'jose';
import { cookies } from 'next/headers';
import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

const DIRECTUS = process.env.DIRECTUS_INTERNAL_URL || 'http://directus:8055';
const TOKEN = process.env.DIRECTUS_STATIC_TOKEN || '';

export const MEMBER_JWT_SECRET =
  process.env.MEMBER_JWT_SECRET ||
  'dev-only-fallback-secret-please-override-in-prod-4f9c2a1e8b6d3c7f0e5a2b9d1c8e4f7a';

export const MEMBER_COOKIE = 'hawkins_member';
const COOKIE_MAX_AGE = 30 * 24 * 60 * 60; // 30 días
const secretKey = new TextEncoder().encode(MEMBER_JWT_SECRET);

export type MemberTier = 'free' | 'premium' | 'vip';

export interface Member {
  id: string;
  email: string;
  name: string;
  password_hash?: string;
  email_verified?: boolean;
  verification_token?: string | null;
  reset_token?: string | null;
  reset_expires?: string | null;
  avatar?: string | null;
  tier: MemberTier;
  last_login?: string | null;
}

async function directusAdmin<T = any>(path: string, init: RequestInit = {}): Promise<T> {
  const res = await fetch(`${DIRECTUS}${path}`, {
    ...init,
    headers: {
      ...(init.headers || {}),
      ...(TOKEN ? { Authorization: `Bearer ${TOKEN}` } : {}),
      'Content-Type': 'application/json',
    },
    cache: 'no-store',
  });
  if (!res.ok) {
    const txt = await res.text().catch(() => '');
    throw new Error(`Directus ${path}: ${res.status} ${txt}`);
  }
  const json = await res.json().catch(() => ({}));
  return (json.data as T) ?? (json as T);
}

// ─────────────── Password helpers ───────────────

export async function hashPassword(plain: string): Promise<string> {
  return bcrypt.hash(plain, 10);
}

export async function verifyPassword(plain: string, hash: string): Promise<boolean> {
  if (!hash) return false;
  return bcrypt.compare(plain, hash);
}

// ─────────────── JWT helpers ───────────────

export async function createToken(memberId: string): Promise<string> {
  return new SignJWT({ sub: memberId })
    .setProtectedHeader({ alg: 'HS256' })
    .setIssuedAt()
    .setExpirationTime('30d')
    .sign(secretKey);
}

export async function verifyToken(token: string): Promise<{ memberId: string } | null> {
  try {
    const { payload } = await jwtVerify(token, secretKey);
    if (!payload.sub) return null;
    return { memberId: String(payload.sub) };
  } catch {
    return null;
  }
}

// ─────────────── Cookie helpers ───────────────

export function setCookie(res: NextResponse, token: string): NextResponse {
  res.cookies.set(MEMBER_COOKIE, token, {
    httpOnly: true,
    secure: true,
    sameSite: 'lax',
    maxAge: COOKIE_MAX_AGE,
    path: '/',
  });
  return res;
}

export function clearCookie(res: NextResponse): NextResponse {
  res.cookies.set(MEMBER_COOKIE, '', {
    httpOnly: true,
    secure: true,
    sameSite: 'lax',
    maxAge: 0,
    path: '/',
  });
  return res;
}

// ─────────────── Session ───────────────

/**
 * Lee la cookie y devuelve el member completo, o null.
 * Puede llamarse sin argumentos (usa next/headers cookies()) o
 * pasándole una NextRequest.
 */
export async function getCurrentMember(req?: NextRequest): Promise<Member | null> {
  let token: string | undefined;
  if (req) {
    token = req.cookies.get(MEMBER_COOKIE)?.value;
  } else {
    try {
      const store = await cookies();
      token = store.get(MEMBER_COOKIE)?.value;
    } catch {
      return null;
    }
  }
  if (!token) return null;
  const payload = await verifyToken(token);
  if (!payload) return null;
  try {
    const m = await directusAdmin<Member>(`/items/members/${payload.memberId}`);
    return m || null;
  } catch {
    return null;
  }
}

// ─────────────── Directus CRUD ───────────────

export async function findByEmail(email: string): Promise<Member | null> {
  const params = new URLSearchParams({
    'filter[email][_eq]': email.toLowerCase(),
    limit: '1',
  });
  try {
    const list = await directusAdmin<Member[]>(`/items/members?${params}`);
    return list?.[0] || null;
  } catch {
    return null;
  }
}

export async function findById(id: string): Promise<Member | null> {
  try {
    return await directusAdmin<Member>(`/items/members/${id}`);
  } catch {
    return null;
  }
}

export async function findByResetToken(token: string): Promise<Member | null> {
  const params = new URLSearchParams({
    'filter[reset_token][_eq]': token,
    limit: '1',
  });
  try {
    const list = await directusAdmin<Member[]>(`/items/members?${params}`);
    return list?.[0] || null;
  } catch {
    return null;
  }
}

export async function createMember(input: {
  email: string;
  password: string;
  name: string;
}): Promise<Member> {
  const hash = await hashPassword(input.password);
  const verification_token = crypto.randomUUID();
  const body = {
    email: input.email.toLowerCase(),
    name: input.name,
    password_hash: hash,
    email_verified: false,
    verification_token,
    tier: 'free' as MemberTier,
  };
  return directusAdmin<Member>('/items/members', {
    method: 'POST',
    body: JSON.stringify(body),
  });
}

export async function updateMember(id: string, patch: Partial<Member>): Promise<Member> {
  return directusAdmin<Member>(`/items/members/${id}`, {
    method: 'PATCH',
    body: JSON.stringify(patch),
  });
}

export async function touchLastLogin(id: string): Promise<void> {
  try {
    await updateMember(id, { last_login: new Date().toISOString() });
  } catch {
    /* no-op */
  }
}

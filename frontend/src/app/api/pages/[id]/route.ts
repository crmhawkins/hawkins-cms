import { NextRequest, NextResponse } from 'next/server';

const DIRECTUS = process.env.DIRECTUS_INTERNAL_URL || 'http://directus:8055';
const TOKEN = process.env.DIRECTUS_STATIC_TOKEN || '';

async function directus(path: string, init?: RequestInit) {
  return fetch(`${DIRECTUS}${path}`, {
    ...init,
    headers: {
      ...(init?.headers || {}),
      Authorization: `Bearer ${TOKEN}`,
      'Content-Type': 'application/json',
    },
    cache: 'no-store',
  });
}

export async function GET(
  _req: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params;
  const res = await directus(`/items/pages/${id}`);
  if (!res.ok) {
    return NextResponse.json({ error: 'Not found' }, { status: res.status });
  }
  const { data } = await res.json();
  return NextResponse.json(data);
}

export async function PATCH(
  req: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params;
  const body = await req.json();
  const res = await directus(`/items/pages/${id}`, {
    method: 'PATCH',
    body: JSON.stringify(body),
  });
  if (!res.ok) {
    const err = await res.text();
    return NextResponse.json({ error: err }, { status: res.status });
  }
  const { data } = await res.json();
  return NextResponse.json(data);
}

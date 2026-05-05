import { NextRequest, NextResponse } from 'next/server';

const DIRECTUS = process.env.DIRECTUS_INTERNAL_URL || 'http://directus:8055';
const TOKEN = process.env.DIRECTUS_STATIC_TOKEN || '';

export async function POST(
  _req: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params;
  const res = await fetch(`${DIRECTUS}/items/pages/${id}`, {
    method: 'PATCH',
    headers: {
      Authorization: `Bearer ${TOKEN}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ status: 'published' }),
  });
  if (!res.ok) {
    return NextResponse.json({ error: 'Failed' }, { status: res.status });
  }
  return NextResponse.json({ ok: true });
}

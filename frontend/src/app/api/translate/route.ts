import { NextRequest, NextResponse } from 'next/server';
import { translate, translateJSON } from '@/lib/translate';

/**
 * POST /api/translate
 * Body: { text | json, from?: string, to: string }
 */
export async function POST(req: NextRequest) {
  try {
    const { text, json, from, to } = await req.json();
    if (!to) {
      return NextResponse.json({ error: 'Missing "to" parameter' }, { status: 400 });
    }

    if (text) {
      const result = await translate(text, { from, to });
      return NextResponse.json({ result });
    }

    if (json) {
      const result = await translateJSON(json, { from, to });
      return NextResponse.json({ result });
    }

    return NextResponse.json({ error: 'Missing "text" or "json" body' }, { status: 400 });
  } catch (e: any) {
    return NextResponse.json({ error: e?.message || 'Error' }, { status: 500 });
  }
}

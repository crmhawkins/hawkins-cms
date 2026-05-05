import { NextResponse } from 'next/server';
import { getModules } from '@/lib/modules';

export async function GET() {
  const mods = await getModules();
  return NextResponse.json(mods);
}

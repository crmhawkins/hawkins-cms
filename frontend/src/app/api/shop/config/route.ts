import { NextResponse } from 'next/server';
import { isModuleEnabled } from '@/lib/modules';
import { getShopConfig } from '@/lib/stripe';

export async function GET() {
  if (!(await isModuleEnabled('ecommerce'))) {
    return NextResponse.json({ error: 'Not found' }, { status: 404 });
  }
  const config = await getShopConfig();
  return NextResponse.json(config);
}

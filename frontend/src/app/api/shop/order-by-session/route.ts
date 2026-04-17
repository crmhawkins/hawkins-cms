import { NextRequest, NextResponse } from 'next/server';
import { isModuleEnabled } from '@/lib/modules';

const DIRECTUS = process.env.DIRECTUS_INTERNAL_URL || 'http://directus:8055';
const TOKEN = process.env.DIRECTUS_STATIC_TOKEN || '';

export async function GET(req: NextRequest) {
  if (!(await isModuleEnabled('ecommerce'))) {
    return NextResponse.json({ error: 'Not found' }, { status: 404 });
  }

  const id = req.nextUrl.searchParams.get('id');
  if (!id) {
    return NextResponse.json({ error: 'Missing id' }, { status: 400 });
  }

  try {
    const q = new URLSearchParams({
      'filter[stripe_session_id][_eq]': id,
      limit: '1',
      fields: 'id,order_number,total,currency,payment_status,date_created',
    });
    const res = await fetch(`${DIRECTUS}/items/orders?${q}`, {
      headers: { Authorization: `Bearer ${TOKEN}` },
      cache: 'no-store',
    });
    const json = await res.json();
    const order = json.data?.[0];
    if (!order) {
      return NextResponse.json({ error: 'Order not found' }, { status: 404 });
    }

    const itemsQ = new URLSearchParams({
      'filter[order_id][_eq]': order.id,
      fields: 'product_name,quantity,unit_price,subtotal',
    });
    const itemsRes = await fetch(`${DIRECTUS}/items/order_items?${itemsQ}`, {
      headers: { Authorization: `Bearer ${TOKEN}` },
      cache: 'no-store',
    });
    const itemsJson = await itemsRes.json();

    return NextResponse.json({
      order_number: order.order_number,
      total: order.total,
      currency: order.currency,
      payment_status: order.payment_status,
      date_created: order.date_created,
      items: itemsJson.data || [],
    });
  } catch (e: any) {
    return NextResponse.json({ error: e.message }, { status: 500 });
  }
}

import { NextRequest, NextResponse } from 'next/server';
import { isModuleEnabled } from '@/lib/modules';
import { getStripe, getShopConfig } from '@/lib/stripe';
import { shop } from '@/lib/shop';

interface ItemInput {
  id: string;
  quantity: number;
}

interface CheckoutBody {
  items: ItemInput[];
  customer_email: string;
  customer_name?: string;
  shipping_address?: string;
}

function makeOrderNumber() {
  const date = new Date();
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  const rand = Math.random().toString(36).slice(2, 7).toUpperCase();
  return `${y}${m}${d}-${rand}`;
}

export async function POST(req: NextRequest) {
  if (!(await isModuleEnabled('ecommerce'))) {
    return NextResponse.json({ error: 'Not found' }, { status: 404 });
  }

  let body: CheckoutBody;
  try {
    body = await req.json();
  } catch {
    return NextResponse.json({ error: 'Invalid JSON' }, { status: 400 });
  }

  if (!body.items?.length) {
    return NextResponse.json({ error: 'El carrito está vacío.' }, { status: 400 });
  }
  if (!body.customer_email) {
    return NextResponse.json({ error: 'Falta el email.' }, { status: 400 });
  }

  const stripe = await getStripe();
  if (!stripe) {
    return NextResponse.json(
      { error: 'Pasarela de pago no configurada.' },
      { status: 500 }
    );
  }
  const config = await getShopConfig();

  // Resolve products server-side (verify prices)
  const resolved = await Promise.all(
    body.items.map(async (it) => {
      const p = await shop.getProductById(it.id);
      if (!p) throw new Error(`Producto no encontrado: ${it.id}`);
      if (p.track_stock && p.stock < it.quantity) {
        throw new Error(`Sin stock suficiente: ${p.name}`);
      }
      return { product: p, quantity: Math.max(1, Math.floor(it.quantity)) };
    })
  ).catch((e: Error) => e);

  if (resolved instanceof Error) {
    return NextResponse.json({ error: resolved.message }, { status: 400 });
  }

  const currency = (resolved[0]?.product.currency || config.currency || 'EUR').toLowerCase();

  let subtotal = 0;
  for (const r of resolved) subtotal += r.product.price * r.quantity;

  const shipping = config.flatShipping || 0;
  const tax = ((subtotal + shipping) * config.taxRate) / 100;
  const total = subtotal + shipping + tax;

  const publicUrl =
    process.env.NEXT_PUBLIC_SITE_URL ||
    process.env.PUBLIC_URL ||
    req.nextUrl.origin;

  const lineItems: any[] = resolved.map((r) => ({
    quantity: r.quantity,
    price_data: {
      currency,
      unit_amount: Math.round(r.product.price * 100),
      product_data: {
        name: r.product.name,
        images: r.product.cover_image ? [`${publicUrl}/assets/${r.product.cover_image}`] : [],
      },
    },
  }));

  if (shipping > 0) {
    lineItems.push({
      quantity: 1,
      price_data: {
        currency,
        unit_amount: Math.round(shipping * 100),
        product_data: { name: 'Envío' },
      },
    });
  }
  if (tax > 0) {
    lineItems.push({
      quantity: 1,
      price_data: {
        currency,
        unit_amount: Math.round(tax * 100),
        product_data: { name: `IVA (${config.taxRate}%)` },
      },
    });
  }

  let session;
  try {
    session = await stripe.checkout.sessions.create({
      mode: 'payment',
      payment_method_types: ['card'],
      customer_email: body.customer_email,
      line_items: lineItems,
      success_url: `${publicUrl}/checkout/success?session_id={CHECKOUT_SESSION_ID}`,
      cancel_url: `${publicUrl}/checkout/cancel`,
      metadata: {
        customer_name: body.customer_name || '',
      },
    });
  } catch (e: any) {
    return NextResponse.json(
      { error: e.message || 'Error de Stripe' },
      { status: 500 }
    );
  }

  // Persist order in Directus
  const orderNumber = makeOrderNumber();
  try {
    await shop.createOrder({
      order_number: orderNumber,
      customer_email: body.customer_email,
      customer_name: body.customer_name,
      shipping_address: body.shipping_address,
      subtotal,
      tax,
      shipping,
      total,
      currency: currency.toUpperCase(),
      payment_status: 'pending',
      stripe_session_id: session.id,
      items: resolved.map((r) => ({
        product_id: r.product.id,
        product_name: r.product.name,
        product_sku: r.product.sku,
        unit_price: r.product.price,
        quantity: r.quantity,
        subtotal: r.product.price * r.quantity,
      })),
    } as any);
  } catch (e) {
    // Order persistence failure shouldn't block checkout. Webhook will log.
    console.error('Order persist failed:', e);
  }

  return NextResponse.json({ url: session.url, session_id: session.id });
}

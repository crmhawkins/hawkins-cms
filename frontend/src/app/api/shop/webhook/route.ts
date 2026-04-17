import { NextRequest, NextResponse } from 'next/server';
import { getStripe } from '@/lib/stripe';
import { cms } from '@/lib/cms';
import { shop } from '@/lib/shop';

export const runtime = 'nodejs';
export const dynamic = 'force-dynamic';

export async function POST(req: NextRequest) {
  const stripe = await getStripe();
  if (!stripe) {
    return NextResponse.json({ error: 'Stripe not configured' }, { status: 500 });
  }

  const settings = await cms.getSettings();
  const webhookSecret = (settings as any)?.stripe_webhook_secret;
  if (!webhookSecret) {
    return NextResponse.json({ error: 'Webhook secret missing' }, { status: 500 });
  }

  const signature = req.headers.get('stripe-signature');
  if (!signature) {
    return NextResponse.json({ error: 'Missing signature' }, { status: 400 });
  }

  const rawBody = await req.text();

  let event;
  try {
    event = stripe.webhooks.constructEvent(rawBody, signature, webhookSecret);
  } catch (err: any) {
    return NextResponse.json(
      { error: `Invalid signature: ${err.message}` },
      { status: 400 }
    );
  }

  try {
    switch (event.type) {
      case 'checkout.session.completed': {
        const session: any = event.data.object;
        await shop.updateOrderPayment(
          session.id,
          'paid',
          session.payment_intent as string | undefined
        );
        break;
      }
      case 'checkout.session.expired': {
        const session: any = event.data.object;
        await shop.updateOrderPayment(session.id, 'failed');
        break;
      }
      case 'charge.refunded': {
        const charge: any = event.data.object;
        // Look up session via payment_intent
        const paymentIntent = charge.payment_intent as string | undefined;
        if (paymentIntent) {
          try {
            const sessions = await stripe.checkout.sessions.list({
              payment_intent: paymentIntent,
              limit: 1,
            });
            if (sessions.data[0]) {
              await shop.updateOrderPayment(sessions.data[0].id, 'refunded', paymentIntent);
            }
          } catch (e) {
            console.error('Refund lookup failed:', e);
          }
        }
        break;
      }
      default:
        break;
    }
  } catch (err) {
    console.error('Webhook handler error:', err);
  }

  return NextResponse.json({ received: true });
}

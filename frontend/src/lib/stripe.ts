/**
 * Cliente Stripe server-side.
 * Carga las claves desde Settings de Directus (no variables de entorno),
 * así cada cliente tiene sus propias claves sin tocar el deploy.
 */
import Stripe from 'stripe';
import { cms } from './cms';

let cached: { stripe: Stripe; key: string } | null = null;

export async function getStripe(): Promise<Stripe | null> {
  const s = await cms.getSettings();
  const key = (s as any)?.stripe_secret_key;
  if (!key) return null;

  if (cached && cached.key === key) return cached.stripe;
  const stripe = new Stripe(key, { apiVersion: '2024-11-20.acacia' as any });
  cached = { stripe, key };
  return stripe;
}

export async function getStripePublishableKey(): Promise<string | null> {
  const s = await cms.getSettings();
  return (s as any)?.stripe_publishable_key || null;
}

export async function getShopConfig() {
  const s = await cms.getSettings();
  return {
    currency: (s as any)?.shop_currency || 'EUR',
    flatShipping: parseFloat((s as any)?.shop_shipping_flat_rate || 0),
    taxRate: parseFloat((s as any)?.shop_tax_rate || 21),
  };
}

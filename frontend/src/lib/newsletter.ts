/**
 * Newsletter: abstrae proveedores (Brevo, Mailerlite, Mailchimp, interno).
 * La config viene de Settings: newsletter_provider, newsletter_api_key, newsletter_list_id
 */
import { cms } from './cms';
import { randomBytes } from 'crypto';

const DIRECTUS = process.env.DIRECTUS_INTERNAL_URL || 'http://directus:8055';
const TOKEN = process.env.DIRECTUS_STATIC_TOKEN || '';

export interface SubscribeInput {
  email: string;
  name?: string;
  source?: string;
  tags?: string[];
}

async function getConfig() {
  const s = await cms.getSettings();
  return {
    provider: (s as any)?.newsletter_provider || 'internal',
    apiKey: (s as any)?.newsletter_api_key || '',
    listId: (s as any)?.newsletter_list_id || '',
  };
}

// ─── Proveedor: Brevo ───────────────────────────
async function subscribeBrevo(input: SubscribeInput, cfg: { apiKey: string; listId: string }): Promise<{ ok: boolean; providerId?: string; error?: string }> {
  try {
    const res = await fetch('https://api.brevo.com/v3/contacts', {
      method: 'POST',
      headers: {
        'api-key': cfg.apiKey,
        'Content-Type': 'application/json',
        accept: 'application/json',
      },
      body: JSON.stringify({
        email: input.email,
        attributes: { FIRSTNAME: input.name },
        listIds: cfg.listId ? [parseInt(cfg.listId)] : undefined,
        updateEnabled: true,
      }),
    });
    if (!res.ok && res.status !== 204) {
      const err = await res.text();
      return { ok: false, error: err };
    }
    const data = res.status === 204 ? {} : await res.json();
    return { ok: true, providerId: (data as any).id?.toString() };
  } catch (e: any) {
    return { ok: false, error: e.message };
  }
}

// ─── Proveedor: Mailerlite ──────────────────────
async function subscribeMailerlite(input: SubscribeInput, cfg: { apiKey: string; listId: string }): Promise<{ ok: boolean; providerId?: string; error?: string }> {
  try {
    const body: any = { email: input.email, fields: {} };
    if (input.name) body.fields.name = input.name;
    if (cfg.listId) body.groups = [cfg.listId];

    const res = await fetch('https://connect.mailerlite.com/api/subscribers', {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${cfg.apiKey}`,
        'Content-Type': 'application/json',
        accept: 'application/json',
      },
      body: JSON.stringify(body),
    });
    if (!res.ok) {
      const err = await res.text();
      return { ok: false, error: err };
    }
    const data = await res.json();
    return { ok: true, providerId: data.data?.id };
  } catch (e: any) {
    return { ok: false, error: e.message };
  }
}

// ─── Proveedor: Mailchimp ───────────────────────
async function subscribeMailchimp(input: SubscribeInput, cfg: { apiKey: string; listId: string }): Promise<{ ok: boolean; providerId?: string; error?: string }> {
  try {
    const dc = cfg.apiKey.split('-')[1];
    if (!dc) return { ok: false, error: 'Mailchimp API key debe contener datacenter (ej. XXX-us1)' };
    const res = await fetch(`https://${dc}.api.mailchimp.com/3.0/lists/${cfg.listId}/members`, {
      method: 'POST',
      headers: {
        Authorization: `Basic ${Buffer.from(`anystring:${cfg.apiKey}`).toString('base64')}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        email_address: input.email,
        status: 'subscribed',
        merge_fields: input.name ? { FNAME: input.name } : undefined,
      }),
    });
    if (!res.ok) {
      const err = await res.text();
      return { ok: false, error: err };
    }
    const data = await res.json();
    return { ok: true, providerId: data.id };
  } catch (e: any) {
    return { ok: false, error: e.message };
  }
}

// ─── Subscribe principal ────────────────────────
export async function subscribe(input: SubscribeInput): Promise<{ ok: boolean; error?: string }> {
  const cfg = await getConfig();

  // Guardar siempre en Directus aunque sea externo (fuente de verdad)
  const unsubscribeToken = randomBytes(24).toString('hex');
  const confirmationToken = randomBytes(24).toString('hex');

  let providerId: string | undefined;
  let providerError: string | undefined;

  if (cfg.provider === 'brevo' && cfg.apiKey) {
    const r = await subscribeBrevo(input, cfg);
    providerId = r.providerId;
    providerError = r.error;
    if (!r.ok) return { ok: false, error: r.error || 'Error con Brevo' };
  } else if (cfg.provider === 'mailerlite' && cfg.apiKey) {
    const r = await subscribeMailerlite(input, cfg);
    providerId = r.providerId;
    providerError = r.error;
    if (!r.ok) return { ok: false, error: r.error || 'Error con Mailerlite' };
  } else if (cfg.provider === 'mailchimp' && cfg.apiKey) {
    const r = await subscribeMailchimp(input, cfg);
    providerId = r.providerId;
    providerError = r.error;
    if (!r.ok) return { ok: false, error: r.error || 'Error con Mailchimp' };
  }

  // Guardar en Directus
  try {
    // Check existing
    const exists = await fetch(`${DIRECTUS}/items/subscribers?filter[email][_eq]=${encodeURIComponent(input.email)}&limit=1`, {
      headers: { Authorization: `Bearer ${TOKEN}` },
    });
    const existingData = await exists.json();

    if (existingData.data && existingData.data.length > 0) {
      return { ok: true }; // Ya estaba suscrito
    }

    await fetch(`${DIRECTUS}/items/subscribers`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${TOKEN}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        email: input.email,
        name: input.name,
        source: input.source,
        tags: input.tags || [],
        confirmed: cfg.provider !== 'internal', // externo confirma solo
        confirmation_token: confirmationToken,
        unsubscribe_token: unsubscribeToken,
        provider_id: providerId,
      }),
    });
    return { ok: true };
  } catch (e: any) {
    return { ok: false, error: e.message };
  }
}

export async function unsubscribeByToken(token: string): Promise<boolean> {
  try {
    const res = await fetch(`${DIRECTUS}/items/subscribers?filter[unsubscribe_token][_eq]=${token}&limit=1`, {
      headers: { Authorization: `Bearer ${TOKEN}` },
    });
    const { data } = await res.json();
    if (!data || data.length === 0) return false;
    await fetch(`${DIRECTUS}/items/subscribers/${data[0].id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${TOKEN}` },
    });
    return true;
  } catch {
    return false;
  }
}

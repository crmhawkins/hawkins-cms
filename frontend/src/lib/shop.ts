/**
 * Helpers para la tienda online.
 */
const DIRECTUS = process.env.DIRECTUS_INTERNAL_URL || 'http://directus:8055';
const TOKEN = process.env.DIRECTUS_STATIC_TOKEN || '';

async function fetchDir<T = any>(path: string, init: RequestInit = {}): Promise<T> {
  const res = await fetch(`${DIRECTUS}${path}`, {
    ...init,
    headers: {
      ...(init.headers || {}),
      ...(TOKEN ? { Authorization: `Bearer ${TOKEN}` } : {}),
      'Content-Type': 'application/json',
    },
    next: { revalidate: 30 },
  });
  if (!res.ok) throw new Error(`Directus: ${res.status}`);
  const json = await res.json();
  return json.data as T;
}

export interface Product {
  id: string;
  status: 'published' | 'draft' | 'archived';
  name: string;
  slug: string;
  sku?: string;
  description?: string;
  short_description?: string;
  price: number;
  compare_price?: number;
  currency: string;
  stock: number;
  track_stock: boolean;
  cover_image?: string;
  gallery?: Array<string>;
  stripe_product_id?: string;
  stripe_price_id?: string;
  featured: boolean;
}

export interface Order {
  id: string;
  order_number: string;
  customer_email: string;
  customer_name?: string;
  total: number;
  currency: string;
  payment_status: string;
  fulfillment_status: string;
  date_created: string;
}

export const shop = {
  listProducts: async (featured = false, limit = 100): Promise<Product[]> => {
    try {
      const q = new URLSearchParams({
        'filter[status][_eq]': 'published',
        limit: String(limit),
      });
      if (featured) q.set('filter[featured][_eq]', 'true');
      return await fetchDir<Product[]>(`/items/products?${q}`);
    } catch {
      return [];
    }
  },

  getProduct: async (slug: string): Promise<Product | null> => {
    try {
      const q = new URLSearchParams({
        'filter[slug][_eq]': slug,
        'filter[status][_eq]': 'published',
        limit: '1',
      });
      const list = await fetchDir<Product[]>(`/items/products?${q}`);
      return list[0] || null;
    } catch {
      return null;
    }
  },

  getProductById: async (id: string): Promise<Product | null> => {
    try {
      return await fetchDir<Product>(`/items/products/${id}`);
    } catch {
      return null;
    }
  },

  createOrder: async (data: Partial<Order> & { items: Array<{ product_id: string; product_name: string; product_sku?: string; unit_price: number; quantity: number; subtotal: number }> }) => {
    const res = await fetch(`${DIRECTUS}/items/orders`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${TOKEN}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        order_number: data.order_number,
        customer_email: data.customer_email,
        customer_name: data.customer_name,
        customer_phone: data.customer_phone,
        shipping_address: data.shipping_address,
        billing_address: data.billing_address,
        subtotal: data.subtotal,
        tax: data.tax,
        shipping: data.shipping,
        total: data.total,
        currency: data.currency,
        payment_status: data.payment_status || 'pending',
        stripe_session_id: data.stripe_session_id,
      }),
    });
    const created = (await res.json()).data;

    // Create order_items
    for (const item of data.items) {
      await fetch(`${DIRECTUS}/items/order_items`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${TOKEN}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ ...item, order_id: created.id }),
      });
    }
    return created;
  },

  updateOrderPayment: async (stripeSessionId: string, status: string, paymentIntentId?: string) => {
    // Find order by stripe_session_id
    const q = new URLSearchParams({ 'filter[stripe_session_id][_eq]': stripeSessionId, limit: '1' });
    const list = await fetchDir<Order[]>(`/items/orders?${q}`);
    if (list.length === 0) return null;

    await fetch(`${DIRECTUS}/items/orders/${list[0].id}`, {
      method: 'PATCH',
      headers: {
        Authorization: `Bearer ${TOKEN}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        payment_status: status,
        stripe_payment_intent_id: paymentIntentId,
      }),
    });
    return list[0];
  },
};

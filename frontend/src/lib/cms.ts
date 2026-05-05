/**
 * Capa de acceso a Directus desde el servidor (SSR).
 * Se usa en server components y route handlers.
 */
const DIRECTUS = process.env.DIRECTUS_INTERNAL_URL || 'http://directus:8055';
const TOKEN = process.env.DIRECTUS_STATIC_TOKEN || '';

async function directusFetch<T = any>(path: string, init: RequestInit = {}): Promise<T> {
  const res = await fetch(`${DIRECTUS}${path}`, {
    ...init,
    headers: {
      ...(init.headers || {}),
      ...(TOKEN ? { Authorization: `Bearer ${TOKEN}` } : {}),
      'Content-Type': 'application/json',
    },
    // Revalidate caché cada 60s en producción, no-cache en dev
    next: { revalidate: 60 },
  });
  if (!res.ok) {
    throw new Error(`Directus ${path}: ${res.status}`);
  }
  const json = await res.json();
  return json.data as T;
}

export interface Page {
  id: string;
  status: 'published' | 'draft' | 'archived';
  title: string;
  slug: string;
  meta_description?: string;
  og_image?: string;
  content?: any;
  header_override?: string | null;
  footer_override?: string | null;
  hide_header: boolean;
  hide_footer: boolean;
}

export interface Header {
  id: string;
  name: string;
  variant: 'transparent' | 'solid_light' | 'solid_dark' | 'minimal';
  logo?: string;
  logo_text?: string;
  cta_label?: string;
  cta_url?: string;
  menu_id?: string;
  is_default: boolean;
}

export interface Footer {
  id: string;
  name: string;
  variant: 'full' | 'minimal' | 'centered';
  columns?: Array<{ title: string; links?: Array<{ label: string; url: string }>; html?: string }>;
  bottom_text?: string;
  show_legal_links: boolean;
  social_links?: Array<{ platform: string; url: string }>;
  is_default: boolean;
}

export interface Menu {
  id: string;
  name: string;
  location: string;
  items: Array<{ label: string; url: string; target?: string; children?: any[] }>;
}

export interface Settings {
  site_name: string;
  site_tagline?: string;
  default_meta_description?: string;
  logo?: string;
  favicon?: string;
  og_image?: string;
  default_locale: string;
  available_locales: string[];
  primary_color: string;
  accent_color: string;
  font_serif: string;
  font_sans: string;
  maintenance_mode: boolean;
  maintenance_password?: string;
  google_analytics_id?: string;
  contact_email?: string;
  contact_phone?: string;
  contact_address?: string;
}

export const cms = {
  getPage: async (slug: string): Promise<Page | null> => {
    const params = new URLSearchParams({
      'filter[slug][_eq]': slug,
      'filter[status][_eq]': 'published',
      limit: '1',
    });
    try {
      const pages = await directusFetch<Page[]>(`/items/pages?${params}`);
      return pages[0] || null;
    } catch {
      return null;
    }
  },

  listPages: async (): Promise<Page[]> => {
    try {
      return await directusFetch<Page[]>('/items/pages?filter[status][_eq]=published&fields=id,title,slug');
    } catch {
      return [];
    }
  },

  getSettings: async (): Promise<Settings | null> => {
    try {
      return await directusFetch<Settings>('/items/settings');
    } catch {
      return null;
    }
  },

  getDefaultHeader: async (): Promise<Header | null> => {
    try {
      const list = await directusFetch<Header[]>('/items/headers?filter[is_default][_eq]=true&limit=1');
      return list[0] || null;
    } catch {
      return null;
    }
  },

  getHeader: async (id: string): Promise<Header | null> => {
    try {
      return await directusFetch<Header>(`/items/headers/${id}`);
    } catch {
      return null;
    }
  },

  getDefaultFooter: async (): Promise<Footer | null> => {
    try {
      const list = await directusFetch<Footer[]>('/items/footers?filter[is_default][_eq]=true&limit=1');
      return list[0] || null;
    } catch {
      return null;
    }
  },

  getFooter: async (id: string): Promise<Footer | null> => {
    try {
      return await directusFetch<Footer>(`/items/footers/${id}`);
    } catch {
      return null;
    }
  },

  getMenu: async (id: string): Promise<Menu | null> => {
    try {
      return await directusFetch<Menu>(`/items/menus/${id}`);
    } catch {
      return null;
    }
  },

  mediaUrl: (fileId?: string | null): string | undefined => {
    if (!fileId) return undefined;
    const pub = process.env.NEXT_PUBLIC_DIRECTUS_URL || '';
    return `${pub}/assets/${fileId}`;
  },
};

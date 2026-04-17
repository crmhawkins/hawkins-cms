/**
 * Acceso al contenido de la zona members.
 */
import type { MemberTier } from './auth-members';

const DIRECTUS = process.env.DIRECTUS_INTERNAL_URL || 'http://directus:8055';
const TOKEN = process.env.DIRECTUS_STATIC_TOKEN || '';

export type ContentType = 'article' | 'video' | 'download' | 'course';

export interface MemberContent {
  id: string;
  title: string;
  slug: string;
  excerpt?: string | null;
  cover_image?: string | null;
  content?: string | null;
  required_tier: MemberTier;
  content_type: ContentType;
  video_url?: string | null;
  download_file?: string | null;
}

async function directusFetch<T = any>(path: string): Promise<T> {
  const res = await fetch(`${DIRECTUS}${path}`, {
    headers: {
      ...(TOKEN ? { Authorization: `Bearer ${TOKEN}` } : {}),
      'Content-Type': 'application/json',
    },
    cache: 'no-store',
  });
  if (!res.ok) throw new Error(`Directus ${path}: ${res.status}`);
  const json = await res.json();
  return json.data as T;
}

const TIER_RANK: Record<MemberTier, number> = {
  free: 0,
  premium: 1,
  vip: 2,
};

/** Devuelve true si memberTier es >= requiredTier (vip > premium > free). */
export function hasAccess(memberTier: MemberTier | undefined, requiredTier: MemberTier): boolean {
  if (!memberTier) return false;
  return TIER_RANK[memberTier] >= TIER_RANK[requiredTier];
}

/** Lista todo el contenido accesible para un tier dado. Si no se pasa, devuelve todo. */
export async function listContent(tier?: MemberTier): Promise<MemberContent[]> {
  try {
    const list = await directusFetch<MemberContent[]>('/items/member_content?limit=200');
    if (!tier) return list || [];
    return (list || []).filter((c) => hasAccess(tier, c.required_tier));
  } catch {
    return [];
  }
}

export async function getContent(slug: string): Promise<MemberContent | null> {
  const params = new URLSearchParams({
    'filter[slug][_eq]': slug,
    limit: '1',
  });
  try {
    const list = await directusFetch<MemberContent[]>(`/items/member_content?${params}`);
    return list?.[0] || null;
  } catch {
    return null;
  }
}

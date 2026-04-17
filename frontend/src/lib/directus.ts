import {
  createDirectus,
  rest,
  staticToken,
  readItems,
  readSingleton,
} from '@directus/sdk';

/**
 * Cliente Directus compartido.
 *
 * En servidor (fetch SSR) usa la URL interna (docker network) si está.
 * En cliente usa la URL pública del admin.
 */
const INTERNAL = process.env.DIRECTUS_INTERNAL_URL;
const PUBLIC = process.env.NEXT_PUBLIC_DIRECTUS_URL || 'http://localhost:8055';
const URL = typeof window === 'undefined' && INTERNAL ? INTERNAL : PUBLIC;

const TOKEN = process.env.DIRECTUS_STATIC_TOKEN || '';

export const directus = createDirectus(URL)
  .with(rest())
  .with(staticToken(TOKEN));

export { readItems, readSingleton };

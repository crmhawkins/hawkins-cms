/**
 * Servicio de traducción automática.
 * Intenta DeepL (si hay API key) y cae a LibreTranslate self-hosted.
 *
 * Uso:
 *   const es = "Hola mundo";
 *   const en = await translate(es, { from: 'es', to: 'en' });
 */

const DEEPL_KEY = process.env.DEEPL_API_KEY;
const LIBRETRANSLATE = process.env.LIBRETRANSLATE_URL || 'http://libretranslate:5000';

const DEEPL_LANGS: Record<string, string> = {
  en: 'EN-GB',
  es: 'ES',
  fr: 'FR',
  de: 'DE',
  pt: 'PT-PT',
  it: 'IT',
  nl: 'NL',
  pl: 'PL',
  ru: 'RU',
  ja: 'JA',
  zh: 'ZH',
};

export type TranslateOpts = { from?: string; to: string };

async function deepl(text: string, { from, to }: TranslateOpts): Promise<string | null> {
  if (!DEEPL_KEY) return null;
  try {
    const res = await fetch('https://api-free.deepl.com/v2/translate', {
      method: 'POST',
      headers: {
        Authorization: `DeepL-Auth-Key ${DEEPL_KEY}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        text: [text],
        target_lang: DEEPL_LANGS[to] || to.toUpperCase(),
        source_lang: from ? DEEPL_LANGS[from] : undefined,
      }),
    });
    if (!res.ok) return null;
    const json = await res.json();
    return json.translations?.[0]?.text || null;
  } catch {
    return null;
  }
}

async function libretranslate(text: string, { from, to }: TranslateOpts): Promise<string | null> {
  try {
    const res = await fetch(`${LIBRETRANSLATE}/translate`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        q: text,
        source: from || 'auto',
        target: to,
        format: 'text',
      }),
    });
    if (!res.ok) return null;
    const json = await res.json();
    return json.translatedText || null;
  } catch {
    return null;
  }
}

export async function translate(text: string, opts: TranslateOpts): Promise<string> {
  if (!text || text.trim() === '') return text;

  const deeplResult = await deepl(text, opts);
  if (deeplResult) return deeplResult;

  const libreResult = await libretranslate(text, opts);
  if (libreResult) return libreResult;

  console.warn('[translate] Both DeepL and LibreTranslate failed, returning original');
  return text;
}

/**
 * Traduce un objeto JSON recursivamente. Solo traduce valores string.
 * Útil para traducir estructuras Puck conservando la forma.
 *
 * Campos ignorados: urls, slugs, colores, IDs, etc.
 */
const SKIP_KEYS = new Set([
  'id',
  'url',
  'href',
  'src',
  'slug',
  'target',
  'color',
  'backgroundImage',
  'image',
  'photo',
  'logo',
  'cover_image',
  'icon',
  'iconName',
  'type',
  'variant',
  'align',
  'height',
  'columns',
  'rows',
  'action',
  'className',
  'style',
]);

export async function translateJSON<T = any>(obj: T, opts: TranslateOpts): Promise<T> {
  if (obj === null || obj === undefined) return obj;
  if (typeof obj === 'string') {
    // Heurística: no traducir URLs, colores hex, números, valores muy cortos tipo slugs
    if (/^https?:\/\//.test(obj)) return obj as any;
    if (/^#[0-9a-fA-F]{3,8}$/.test(obj)) return obj as any;
    if (/^[a-z0-9_-]+$/.test(obj) && obj.length < 20) return obj as any;
    if (obj.trim() === '') return obj as any;
    return (await translate(obj, opts)) as any;
  }
  if (Array.isArray(obj)) {
    return (await Promise.all(obj.map((x) => translateJSON(x, opts)))) as any;
  }
  if (typeof obj === 'object') {
    const result: any = {};
    for (const [k, v] of Object.entries(obj)) {
      if (SKIP_KEYS.has(k)) {
        result[k] = v;
      } else {
        result[k] = await translateJSON(v, opts);
      }
    }
    return result;
  }
  return obj;
}

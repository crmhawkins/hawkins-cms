import { NextRequest, NextResponse } from 'next/server';
import { templates } from '@/templates';

const DIRECTUS = process.env.DIRECTUS_INTERNAL_URL || 'http://directus:8055';
const TOKEN = process.env.DIRECTUS_STATIC_TOKEN || '';

/**
 * POST /api/templates/apply
 * Body: { templateId: string }
 *
 * Crea todas las páginas del template en Directus.
 * Las páginas existentes con el mismo slug NO se sobrescriben por defecto.
 */
export async function POST(req: NextRequest) {
  const { templateId, overwrite = false } = await req.json();
  const tpl = templates.find((t) => t.id === templateId);
  if (!tpl) {
    return NextResponse.json({ error: 'Template not found' }, { status: 404 });
  }

  const created: string[] = [];
  const skipped: string[] = [];

  for (const [slug, content] of Object.entries(tpl.pages)) {
    // Check if page exists
    const existing = await fetch(
      `${DIRECTUS}/items/pages?filter[slug][_eq]=${slug}&limit=1`,
      { headers: { Authorization: `Bearer ${TOKEN}` } }
    );
    const existingData = await existing.json();
    const exists = existingData.data && existingData.data.length > 0;

    if (exists && !overwrite) {
      skipped.push(slug);
      continue;
    }

    const title = slug.charAt(0).toUpperCase() + slug.slice(1).replace(/-/g, ' ');

    if (exists && overwrite) {
      // Update
      await fetch(`${DIRECTUS}/items/pages/${existingData.data[0].id}`, {
        method: 'PATCH',
        headers: {
          Authorization: `Bearer ${TOKEN}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ content, status: 'published' }),
      });
    } else {
      // Create
      await fetch(`${DIRECTUS}/items/pages`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${TOKEN}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          title,
          slug,
          content,
          status: 'published',
        }),
      });
    }
    created.push(slug);
  }

  return NextResponse.json({
    ok: true,
    templateId,
    templateName: tpl.name,
    created,
    skipped,
  });
}

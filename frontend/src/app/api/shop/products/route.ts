import { NextRequest, NextResponse } from 'next/server';
import { isModuleEnabled } from '@/lib/modules';
import { shop } from '@/lib/shop';

export async function GET(req: NextRequest) {
  if (!(await isModuleEnabled('ecommerce'))) {
    return NextResponse.json({ error: 'Not found' }, { status: 404 });
  }

  const source = req.nextUrl.searchParams.get('source') || 'all';
  const limit = parseInt(req.nextUrl.searchParams.get('limit') || '12', 10);
  const slugsParam = req.nextUrl.searchParams.get('slugs');

  let products = await shop.listProducts(source === 'featured', 100);

  if (source === 'manual' && slugsParam) {
    const slugs = slugsParam.split(',').map((s) => s.trim()).filter(Boolean);
    products = products.filter((p) => slugs.includes(p.slug));
    // Preserve requested order
    products.sort((a, b) => slugs.indexOf(a.slug) - slugs.indexOf(b.slug));
  }

  products = products.slice(0, limit);

  return NextResponse.json({
    data: products.map((p) => ({
      id: p.id,
      name: p.name,
      slug: p.slug,
      price: p.price,
      compare_price: p.compare_price,
      currency: p.currency,
      short_description: p.short_description,
      cover_image: p.cover_image,
      featured: p.featured,
    })),
  });
}

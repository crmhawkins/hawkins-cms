import { NextRequest, NextResponse } from 'next/server';
import { isModuleEnabled } from '@/lib/modules';
import { shop } from '@/lib/shop';

export async function GET(
  _req: NextRequest,
  { params }: { params: Promise<{ slug: string }> }
) {
  if (!(await isModuleEnabled('ecommerce'))) {
    return NextResponse.json({ error: 'Not found' }, { status: 404 });
  }

  const { slug } = await params;
  const p = await shop.getProduct(slug);
  if (!p) {
    return NextResponse.json({ error: 'Not found' }, { status: 404 });
  }

  return NextResponse.json({
    id: p.id,
    name: p.name,
    slug: p.slug,
    price: p.price,
    compare_price: p.compare_price,
    currency: p.currency,
    short_description: p.short_description,
    description: p.description,
    cover_image: p.cover_image,
    gallery: p.gallery,
    featured: p.featured,
    stock: p.stock,
    track_stock: p.track_stock,
  });
}

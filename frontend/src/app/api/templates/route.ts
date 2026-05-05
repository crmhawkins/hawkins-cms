import { NextResponse } from 'next/server';
import { templates, templateCategories } from '@/templates';

export async function GET() {
  return NextResponse.json({
    categories: templateCategories,
    templates: templates.map((t) => ({
      id: t.id,
      name: t.name,
      description: t.description,
      category: t.category,
      thumbnail: t.thumbnail,
      pageCount: Object.keys(t.pages).length,
    })),
  });
}

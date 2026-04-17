import { notFound } from 'next/navigation';
import { Render } from '@measured/puck';
import type { Metadata } from 'next';
import { puckConfig } from '@/blocks';
import { cms } from '@/lib/cms';
import { SiteHeader } from '@/components/SiteHeader';
import { SiteFooter } from '@/components/SiteFooter';

export const revalidate = 60;

type Props = {
  params: Promise<{ slug?: string[] }>;
};

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const path = slug?.join('/') || 'home';
  const page = await cms.getPage(path);
  const settings = await cms.getSettings();

  if (!page) return { title: 'No encontrado' };

  return {
    title: page.title,
    description: page.meta_description || settings?.default_meta_description,
    openGraph: {
      title: page.title,
      description: page.meta_description || settings?.default_meta_description,
      images: page.og_image
        ? [{ url: cms.mediaUrl(page.og_image)! }]
        : settings?.og_image
        ? [{ url: cms.mediaUrl(settings.og_image)! }]
        : undefined,
    },
  };
}

export default async function DynamicPage({ params }: Props) {
  const { slug } = await params;
  const path = slug?.join('/') || 'home';
  const page = await cms.getPage(path);

  if (!page) notFound();

  // Header
  const header = page.hide_header
    ? null
    : page.header_override
    ? await cms.getHeader(page.header_override)
    : await cms.getDefaultHeader();

  // Footer
  const footer = page.hide_footer
    ? null
    : page.footer_override
    ? await cms.getFooter(page.footer_override)
    : await cms.getDefaultFooter();

  return (
    <>
      {header && <SiteHeader header={header} />}

      <main>
        {page.content ? (
          <Render config={puckConfig} data={page.content} />
        ) : (
          <div className="min-h-[50vh] flex items-center justify-center">
            <p className="text-neutral-500">Esta página aún no tiene contenido.</p>
          </div>
        )}
      </main>

      {footer && <SiteFooter footer={footer} />}
    </>
  );
}

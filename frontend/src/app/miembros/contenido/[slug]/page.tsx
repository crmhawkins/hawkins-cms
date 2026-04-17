import { notFound, redirect } from 'next/navigation';
import Link from 'next/link';
import { requireModule } from '@/lib/modules';
import { getCurrentMember } from '@/lib/auth-members';
import { getContent, hasAccess } from '@/lib/member-content';

const TIER_LABEL: Record<string, string> = {
  free: 'Free',
  premium: 'Premium',
  vip: 'VIP',
};

function toEmbedUrl(url: string): string {
  // YouTube
  const yt = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([\w-]{11})/);
  if (yt) return `https://www.youtube.com/embed/${yt[1]}`;
  // Vimeo
  const vm = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
  if (vm) return `https://player.vimeo.com/video/${vm[1]}`;
  return url;
}

export default async function ContentDetailPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  await requireModule('members');
  const member = await getCurrentMember();
  if (!member) redirect('/miembros/login');

  const { slug } = await params;
  const content = await getContent(slug);
  if (!content) notFound();

  const canView = hasAccess(member.tier, content.required_tier);

  return (
    <main className="min-h-screen bg-neutral-50 px-6 py-16">
      <div className="max-w-3xl mx-auto">
        <div className="mb-8">
          <Link
            href="/miembros/contenido"
            className="text-xs tracking-[0.2em] uppercase text-neutral-500 hover:text-neutral-900"
          >
            ← Contenido
          </Link>
        </div>

        {content.cover_image && (
          <div className="aspect-[16/9] bg-neutral-200 mb-8 overflow-hidden">
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img src={content.cover_image} alt={content.title} className="w-full h-full object-cover" />
          </div>
        )}

        <div className="flex items-center gap-2 mb-4">
          <span className="inline-block text-[10px] tracking-[0.25em] uppercase bg-neutral-900 text-white px-2 py-1">
            {content.content_type}
          </span>
          <span className="inline-block text-[10px] tracking-[0.25em] uppercase border border-neutral-300 text-neutral-600 px-2 py-1">
            {content.required_tier}
          </span>
        </div>
        <h1 className="font-serif text-4xl md:text-5xl text-neutral-900 mb-6 leading-tight">
          {content.title}
        </h1>
        {content.excerpt && (
          <p className="text-base text-neutral-700 mb-10 leading-relaxed">{content.excerpt}</p>
        )}

        {!canView ? (
          <div className="bg-white border border-neutral-200 p-10 text-center">
            <h2 className="font-serif text-2xl text-neutral-900 mb-3">Contenido restringido</h2>
            <p className="text-sm text-neutral-600 mb-6">
              Necesitas un plan <strong>{TIER_LABEL[content.required_tier]}</strong> o superior para
              acceder a este contenido. Tu plan actual es{' '}
              <strong>{TIER_LABEL[member.tier] || member.tier}</strong>.
            </p>
            <Link
              href="/miembros"
              className="inline-block bg-neutral-900 text-white px-6 py-3 text-xs tracking-[0.25em] uppercase hover:bg-neutral-700 transition"
            >
              Volver al panel
            </Link>
          </div>
        ) : (
          <article className="bg-white border border-neutral-200 p-8 md:p-12">
            {content.content_type === 'video' && content.video_url && (
              <div className="aspect-video mb-8 bg-black">
                <iframe
                  src={toEmbedUrl(content.video_url)}
                  className="w-full h-full"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                  allowFullScreen
                />
              </div>
            )}
            {content.content_type === 'download' && content.download_file && (
              <div className="mb-8">
                <a
                  href={content.download_file}
                  download
                  className="inline-block bg-neutral-900 text-white px-6 py-3 text-xs tracking-[0.25em] uppercase hover:bg-neutral-700 transition"
                >
                  Descargar archivo
                </a>
              </div>
            )}
            {content.content && (
              <div
                className="prose prose-neutral max-w-none"
                dangerouslySetInnerHTML={{ __html: content.content }}
              />
            )}
          </article>
        )}
      </div>
    </main>
  );
}

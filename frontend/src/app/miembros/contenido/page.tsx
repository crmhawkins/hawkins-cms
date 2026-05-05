import { redirect } from 'next/navigation';
import Link from 'next/link';
import { requireModule } from '@/lib/modules';
import { getCurrentMember } from '@/lib/auth-members';
import { listContent } from '@/lib/member-content';

const TYPE_LABEL: Record<string, string> = {
  article: 'Artículo',
  video: 'Vídeo',
  download: 'Descarga',
  course: 'Curso',
};

export default async function ContentListPage() {
  await requireModule('members');
  const member = await getCurrentMember();
  if (!member) redirect('/miembros/login');

  const items = await listContent(member.tier);

  return (
    <main className="min-h-screen bg-neutral-50 px-6 py-16">
      <div className="max-w-6xl mx-auto">
        <div className="mb-8">
          <Link
            href="/miembros"
            className="text-xs tracking-[0.2em] uppercase text-neutral-500 hover:text-neutral-900"
          >
            ← Volver
          </Link>
        </div>
        <h1 className="font-serif text-4xl md:text-5xl text-neutral-900 mb-3">Contenido</h1>
        <p className="text-sm text-neutral-600 mb-12">
          Contenido disponible para tu plan.
        </p>
        {items.length === 0 ? (
          <p className="text-sm text-neutral-500">No hay contenido disponible todavía.</p>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {items.map((c) => (
              <Link
                key={c.id}
                href={`/miembros/contenido/${c.slug}`}
                className="group block bg-white border border-neutral-200 hover:border-neutral-900 transition"
              >
                <div className="aspect-[4/3] bg-neutral-100 overflow-hidden">
                  {c.cover_image ? (
                    // eslint-disable-next-line @next/next/no-img-element
                    <img
                      src={c.cover_image}
                      alt={c.title}
                      className="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                    />
                  ) : null}
                </div>
                <div className="p-5">
                  <div className="flex items-center gap-2 mb-3">
                    <span className="inline-block text-[10px] tracking-[0.25em] uppercase bg-neutral-900 text-white px-2 py-1">
                      {TYPE_LABEL[c.content_type] || c.content_type}
                    </span>
                    <span className="inline-block text-[10px] tracking-[0.25em] uppercase border border-neutral-300 text-neutral-600 px-2 py-1">
                      {c.required_tier}
                    </span>
                  </div>
                  <h2 className="font-serif text-lg text-neutral-900 mb-2 leading-snug">
                    {c.title}
                  </h2>
                  {c.excerpt && (
                    <p className="text-sm text-neutral-600 leading-relaxed line-clamp-3">
                      {c.excerpt}
                    </p>
                  )}
                </div>
              </Link>
            ))}
          </div>
        )}
      </div>
    </main>
  );
}

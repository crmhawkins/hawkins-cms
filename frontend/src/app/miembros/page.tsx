import { redirect } from 'next/navigation';
import Link from 'next/link';
import { requireModule } from '@/lib/modules';
import { getCurrentMember } from '@/lib/auth-members';

const TIER_LABEL: Record<string, string> = {
  free: 'Free',
  premium: 'Premium',
  vip: 'VIP',
};

export default async function MembersDashboard() {
  await requireModule('members');
  const member = await getCurrentMember();
  if (!member) redirect('/miembros/login');

  return (
    <main className="min-h-screen bg-neutral-50 px-6 py-16">
      <div className="max-w-4xl mx-auto">
        <header className="mb-12">
          <p className="text-xs tracking-[0.3em] uppercase text-neutral-500 mb-2">
            Área privada
          </p>
          <h1 className="font-serif text-4xl md:text-5xl text-neutral-900">
            Hola, {member.name}
          </h1>
          <p className="mt-3 text-sm text-neutral-600">
            Tu plan actual:{' '}
            <span className="inline-block bg-neutral-900 text-white text-xs tracking-[0.2em] uppercase px-3 py-1 ml-1">
              {TIER_LABEL[member.tier] || member.tier}
            </span>
          </p>
        </header>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <DashCard
            href="/miembros/contenido"
            title="Contenido"
            body="Accede al contenido disponible para tu plan."
          />
          <DashCard
            href="/miembros/perfil"
            title="Mi perfil"
            body="Edita tu nombre, avatar y contraseña."
          />
          <DashCard
            href="/miembros/logout"
            title="Cerrar sesión"
            body="Sal de tu cuenta en este dispositivo."
          />
        </div>
      </div>
    </main>
  );
}

function DashCard({ href, title, body }: { href: string; title: string; body: string }) {
  return (
    <Link
      href={href}
      className="block bg-white border border-neutral-200 p-6 hover:border-neutral-900 transition"
    >
      <h2 className="font-serif text-xl text-neutral-900 mb-2">{title}</h2>
      <p className="text-sm text-neutral-600 leading-relaxed">{body}</p>
    </Link>
  );
}

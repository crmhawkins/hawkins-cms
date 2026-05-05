import Link from 'next/link';
import { cms } from '@/lib/cms';

export const revalidate = 0;

/**
 * Página post-login. Sirve como hub de bienvenida al cliente.
 * Muestra accesos rápidos a lo que más se usa.
 */
export default async function WelcomePage() {
  const settings = await cms.getSettings();
  const pages = await cms.listPages();

  const quickLinks = [
    { label: 'Editar página de inicio', url: '/editor/' + (pages.find(p => p.slug === 'home')?.id || ''), icon: '📝', enabled: !!pages.find(p => p.slug === 'home') },
    { label: 'Todas las páginas', url: '/admin/content/pages', icon: '📄', enabled: true },
    { label: 'Portfolio / Proyectos', url: '/admin/content/projects', icon: '🖼️', enabled: true },
    { label: 'Imágenes', url: '/admin/files', icon: '🎨', enabled: true },
    { label: 'Configuración', url: '/admin/settings', icon: '⚙️', enabled: true },
  ];

  return (
    <main className="min-h-screen bg-neutral-50 py-20 px-6">
      <div className="max-w-4xl mx-auto">
        <p className="text-xs tracking-[0.3em] uppercase text-neutral-400 mb-4">
          Bienvenido
        </p>
        <h1 className="font-serif text-5xl mb-3 text-neutral-900">
          Hola 👋
        </h1>
        <p className="text-lg text-neutral-600 mb-12 max-w-2xl">
          Este es el panel de <strong>{settings?.site_name || 'tu sitio'}</strong>.
          Desde aquí editas todo el contenido de la web.
        </p>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-3 mb-12">
          {quickLinks.filter((l) => l.enabled).map((link, i) => (
            <Link
              key={i}
              href={link.url}
              className="group bg-white border border-neutral-200 rounded-lg p-6 hover:border-black transition flex items-center gap-4"
            >
              <span className="text-3xl">{link.icon}</span>
              <div className="flex-1">
                <div className="text-sm font-medium text-neutral-900 group-hover:text-black">
                  {link.label}
                </div>
              </div>
              <span className="text-neutral-300 group-hover:text-black text-xl">→</span>
            </Link>
          ))}
        </div>

        <div className="bg-black text-white rounded-lg p-8">
          <h2 className="font-serif text-2xl mb-3">¿Primera vez por aquí?</h2>
          <p className="text-white/70 mb-6 max-w-xl">
            Si acabas de estrenar el sitio, puedes empezar aplicando una plantilla
            con contenido demo. Luego la personalizas a tu gusto.
          </p>
          <Link
            href="/admin/content/pages"
            className="inline-block bg-white text-black px-6 py-3 text-xs tracking-[0.2em] uppercase hover:bg-neutral-200 transition"
          >
            Ir al panel
          </Link>
        </div>
      </div>
    </main>
  );
}

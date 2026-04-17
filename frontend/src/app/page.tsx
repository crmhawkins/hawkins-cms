import Link from 'next/link';

export default function HomePage() {
  return (
    <main className="min-h-screen flex items-center justify-center p-6">
      <div className="max-w-2xl text-center space-y-6">
        <p className="text-xs tracking-[0.3em] uppercase text-neutral-500">
          hawkins-cms
        </p>
        <h1 className="font-serif text-5xl md:text-6xl font-light">
          Tu web todavía no tiene contenido
        </h1>
        <p className="text-neutral-600 leading-relaxed">
          Este sitio está listo para usarse con <strong>hawkins-cms</strong>.
          Entra al panel de administración, crea tu página de inicio y
          publícala. Aparecerá aquí automáticamente.
        </p>
        <div className="flex gap-3 justify-center pt-4">
          <Link
            href="/admin"
            className="inline-block bg-black text-white px-6 py-3 text-xs tracking-[0.2em] uppercase hover:bg-neutral-800 transition"
          >
            Entrar al panel
          </Link>
          <a
            href="https://github.com/crmhawkins/hawkins-cms"
            target="_blank"
            rel="noopener noreferrer"
            className="inline-block border border-black text-black px-6 py-3 text-xs tracking-[0.2em] uppercase hover:bg-black hover:text-white transition"
          >
            Documentación
          </a>
        </div>
      </div>
    </main>
  );
}

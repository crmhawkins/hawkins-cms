import type { Footer } from '@/lib/cms';
import Link from 'next/link';

export function SiteFooter({ footer }: { footer: Footer }) {
  if (footer.variant === 'minimal') {
    return (
      <footer className="bg-black text-white py-6 px-6 border-t border-white/10">
        <div className="max-w-7xl mx-auto flex items-center justify-between text-xs">
          <span className="text-white">{footer.bottom_text || '© 2026'}</span>
          {footer.show_legal_links && (
            <div className="flex gap-4 text-white/50">
              <Link href="/politica-privacidad" className="hover:text-white">Privacidad</Link>
              <Link href="/terminos-condiciones" className="hover:text-white">Términos</Link>
            </div>
          )}
        </div>
      </footer>
    );
  }

  if (footer.variant === 'centered') {
    return (
      <footer className="bg-black text-white py-16 px-6 text-center border-t border-white/10">
        <div className="max-w-2xl mx-auto space-y-4">
          <p className="text-lg font-serif tracking-[0.2em]">{footer.bottom_text || '© 2026'}</p>
          {footer.social_links && footer.social_links.length > 0 && (
            <div className="flex justify-center gap-4">
              {footer.social_links.map((s, i) => (
                <a key={i} href={s.url} target="_blank" rel="noopener" className="text-white/60 hover:text-white">
                  {s.platform}
                </a>
              ))}
            </div>
          )}
          {footer.show_legal_links && (
            <div className="flex justify-center gap-4 text-xs text-white/50 pt-4">
              <Link href="/politica-privacidad" className="hover:text-white">Privacidad</Link>
              <Link href="/terminos-condiciones" className="hover:text-white">Términos</Link>
            </div>
          )}
        </div>
      </footer>
    );
  }

  // variant "full"
  return (
    <footer className="bg-black text-white px-6 pt-16 pb-6 border-t border-white/10">
      <div className="max-w-7xl mx-auto">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
          {footer.columns?.map((col, i) => (
            <div key={i}>
              <h5 className="text-xs font-semibold tracking-[0.2em] uppercase mb-4">
                {col.title}
              </h5>
              {col.links ? (
                <ul className="space-y-2">
                  {col.links.map((l, j) => (
                    <li key={j}>
                      <Link href={l.url} className="text-sm text-white/60 hover:text-white transition">
                        {l.label}
                      </Link>
                    </li>
                  ))}
                </ul>
              ) : (
                <div className="text-sm text-white/60" dangerouslySetInnerHTML={{ __html: col.html || '' }} />
              )}
            </div>
          ))}
        </div>

        <div className="pt-6 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs">
          <p className="text-white">{footer.bottom_text || '© 2026'}</p>
          {footer.show_legal_links && (
            <div className="flex gap-4 text-white/50">
              <Link href="/politica-privacidad" className="hover:text-white">Política de Privacidad</Link>
              <Link href="/terminos-condiciones" className="hover:text-white">Términos y Condiciones</Link>
            </div>
          )}
        </div>
      </div>
    </footer>
  );
}

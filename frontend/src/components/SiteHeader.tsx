import type { Header, Menu } from '@/lib/cms';
import { cms } from '@/lib/cms';
import Link from 'next/link';

export async function SiteHeader({ header }: { header: Header }) {
  const menu = header.menu_id ? await cms.getMenu(header.menu_id) : null;
  const logoSrc = cms.mediaUrl(header.logo);

  const variantClass = {
    transparent: 'absolute top-0 left-0 right-0 z-40 text-white',
    solid_light: 'bg-white/95 backdrop-blur-md border-b border-neutral-200 text-black',
    solid_dark: 'bg-black text-white',
    minimal: 'border-b border-neutral-200 text-black',
  }[header.variant] || '';

  return (
    <header className={`${variantClass} px-6 py-5`}>
      <div className="max-w-7xl mx-auto flex items-center justify-between">
        <Link href="/" className="font-serif text-lg tracking-[0.25em] uppercase">
          {logoSrc ? (
            <img src={logoSrc} alt={header.logo_text || 'Logo'} className="h-8 w-auto" />
          ) : (
            header.logo_text || 'LOGO'
          )}
        </Link>

        {menu && (
          <nav className="hidden md:flex items-center gap-8">
            {menu.items?.map((item, i) => (
              <Link
                key={i}
                href={item.url}
                target={item.target || '_self'}
                className="text-xs tracking-[0.15em] uppercase hover:opacity-70 transition"
              >
                {item.label}
              </Link>
            ))}
            {header.cta_label && header.cta_url && (
              <Link
                href={header.cta_url}
                className="px-4 py-2 text-xs tracking-[0.2em] uppercase border border-current hover:bg-current hover:text-white transition"
              >
                {header.cta_label}
              </Link>
            )}
          </nav>
        )}
      </div>
    </header>
  );
}

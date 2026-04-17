import type { Metadata } from 'next';
import './globals.css';
import { cms } from '@/lib/cms';

export async function generateMetadata(): Promise<Metadata> {
  const s = await cms.getSettings();
  return {
    title: {
      default: s?.site_name || 'Hawkins CMS',
      template: `%s — ${s?.site_name || 'Hawkins CMS'}`,
    },
    description: s?.default_meta_description || 'Construido con hawkins-cms',
    icons: s?.favicon ? [{ url: cms.mediaUrl(s.favicon)! }] : [],
  };
}

export default async function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const s = await cms.getSettings();
  const locale = s?.default_locale || 'es';

  return (
    <html lang={locale}>
      <head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />
        <link
          href={`https://fonts.googleapis.com/css2?family=${encodeURIComponent(
            s?.font_serif || 'Cormorant Garamond'
          ).replace('%20', '+')}:ital,wght@0,300;0,400;0,500;1,300&family=${encodeURIComponent(
            s?.font_sans || 'Montserrat'
          ).replace('%20', '+')}:wght@300;400;500;600&display=swap`}
          rel="stylesheet"
        />
        <style
          dangerouslySetInnerHTML={{
            __html: `:root {
              --primary: ${s?.primary_color || '#0a0a0a'};
              --accent: ${s?.accent_color || '#888888'};
              --font-serif: '${s?.font_serif || 'Cormorant Garamond'}', Georgia, serif;
              --font-sans: '${s?.font_sans || 'Montserrat'}', system-ui, sans-serif;
            }`,
          }}
        />
        {s?.google_analytics_id && (
          <script
            async
            src={`https://www.googletagmanager.com/gtag/js?id=${s.google_analytics_id}`}
          />
        )}
      </head>
      <body>{children}</body>
    </html>
  );
}

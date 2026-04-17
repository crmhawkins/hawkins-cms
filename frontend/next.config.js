/** @type {import('next').NextConfig} */
const nextConfig = {
  output: 'standalone',
  reactStrictMode: true,
  images: {
    remotePatterns: [
      { protocol: 'https', hostname: '**' },
      { protocol: 'http', hostname: '**' },
    ],
  },
  experimental: {
    serverActions: { bodySizeLimit: '10mb' },
  },
  // Las rutas /admin/*, /api/directus/*, /assets/* se proxy-pasan a Directus
  async rewrites() {
    const internal = process.env.DIRECTUS_INTERNAL_URL || 'http://directus:8055';
    return [
      { source: '/admin', destination: `${internal}/admin` },
      { source: '/admin/:path*', destination: `${internal}/admin/:path*` },
      { source: '/api/directus/:path*', destination: `${internal}/:path*` },
      { source: '/assets/:path*', destination: `${internal}/assets/:path*` },
      { source: '/auth/:path*', destination: `${internal}/auth/:path*` },
    ];
  },
  async redirects() {
    return [
      { source: '/login', destination: '/admin/login', permanent: false },
    ];
  },
};

module.exports = nextConfig;

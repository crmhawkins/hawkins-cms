#!/usr/bin/env bash
# ══════════════════════════════════════════════════════════════
# hawkins-cms — setup local
# Genera un .env con secretos fuertes y levanta los servicios.
# Uso:  bash scripts/setup.sh
# ══════════════════════════════════════════════════════════════
set -e

if [ -f .env ]; then
  echo "⚠️  .env ya existe. Borralo o copia .env.example manualmente si quieres regenerarlo."
  exit 1
fi

rand() { node -e "console.log(require('crypto').randomBytes($1).toString('hex'))"; }

cat > .env <<EOF
# Generado automáticamente por scripts/setup.sh en $(date)

PUBLIC_URL=http://localhost:3000

# MariaDB
MARIADB_ROOT_PASSWORD=$(rand 16)
MARIADB_DATABASE=hawkins_cms
MARIADB_USER=hawkins
MARIADB_PASSWORD=$(rand 16)

# Directus
DIRECTUS_KEY=$(rand 32)
DIRECTUS_SECRET=$(rand 32)
DIRECTUS_ADMIN_EMAIL=admin@localhost.dev
DIRECTUS_ADMIN_PASSWORD=$(rand 12)
DIRECTUS_STATIC_TOKEN=

# MinIO
MINIO_ROOT_USER=minioadmin
MINIO_ROOT_PASSWORD=$(rand 16)
MINIO_BUCKET=media

# Email (opcional)
EMAIL_FROM=noreply@localhost
EMAIL_TRANSPORT=sendmail
EMAIL_SMTP_HOST=
EMAIL_SMTP_PORT=587
EMAIL_SMTP_USER=
EMAIL_SMTP_PASSWORD=
EMAIL_SMTP_SECURE=false

# Traducción
DEEPL_API_KEY=
LT_LANGUAGES=en,es,fr,de,pt,it,ca
EOF

echo "✓ .env generado con secretos fuertes."
echo
echo "Credenciales de administrador:"
grep -E "DIRECTUS_ADMIN_EMAIL|DIRECTUS_ADMIN_PASSWORD" .env
echo
echo "Siguiente paso:"
echo "  docker compose up -d"
echo
echo "Cuando todo esté levantado:"
echo "  Web pública : http://localhost:3000"
echo "  Admin CMS   : http://localhost:3000/admin"
echo "  phpMyAdmin  : http://localhost:8081"
echo "  MinIO       : http://localhost:9001"

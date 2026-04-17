# Despliegue de hawkins-cms en Coolify

Guía para desplegar una nueva instancia del CMS (un sitio = una instancia) en
un servidor Coolify.

## Prerequisitos

- Coolify instalado y funcionando
- Un dominio apuntado al servidor Coolify (con DNS ya propagado)
- Acceso a este repositorio

## Paso 1 — Nueva aplicación en Coolify

1. En Coolify → **+ New** → **Docker Compose**
2. Repository URL: `https://github.com/crmhawkins/hawkins-cms`
3. Branch: `main`
4. Base Directory: `/`
5. Docker Compose Location: `docker-compose.yml`

## Paso 2 — Variables de entorno

Copia las variables de `.env.example` a Coolify (**Environment Variables**).

**Generación rápida de secretos:**

En tu terminal local:
```bash
node -e "console.log(require('crypto').randomBytes(32).toString('hex'))"
```

Ejecútalo para cada uno de estos valores (no reutilices):
- `MARIADB_ROOT_PASSWORD`
- `MARIADB_PASSWORD`
- `DIRECTUS_KEY`
- `DIRECTUS_SECRET`
- `DIRECTUS_ADMIN_PASSWORD`
- `MINIO_ROOT_PASSWORD`

Valor obligatorio único por cliente:
- `PUBLIC_URL=https://sanzahra.com`  (el dominio del cliente)
- `DIRECTUS_ADMIN_EMAIL=admin@sanzahra.com`

## Paso 3 — Dominios y rutas

Coolify usa Traefik. Configura los dominios de cada servicio así:

| Servicio interno | Puerto | Dominio sugerido |
|---|---|---|
| `frontend`      | 3000 | `sanzahra.com` + Path rule: `PathPrefix(/)` |
| `directus`      | 8055 | `sanzahra.com` + Path rule: `PathPrefix(/admin) \|\| PathPrefix(/api/directus) \|\| PathPrefix(/assets) \|\| PathPrefix(/auth)` |
| `phpmyadmin`    | 80   | `sanzahra.com` + Path rule: `PathPrefix(/pma)` (protegido con basic auth) |
| `minio` (API)   | 9000 | `media.sanzahra.com` (opcional, para URLs públicas de imágenes) |

> El `next.config.js` del frontend ya contiene `rewrites` que redirigen
> `/admin/*`, `/api/directus/*`, `/assets/*`, `/auth/*` al contenedor interno
> de Directus. Esto permite tener TODO bajo el mismo dominio sin configurar
> rutas en Traefik. **Recomendado**: apunta solo el `frontend` al dominio.

## Paso 4 — Deploy

Click en **Deploy** en Coolify. Tardará 3-5 minutos en descargar imágenes,
crear DB y arrancar todos los contenedores.

## Paso 5 — Primer login

1. Abre `https://tudominio.com/login`
2. Email y contraseña: los que pusiste en `DIRECTUS_ADMIN_EMAIL` y `DIRECTUS_ADMIN_PASSWORD`
3. Ve a **Settings → Access Tokens** y crea un "Static Token" con rol Administrator.
4. Copia el token a la variable `DIRECTUS_STATIC_TOKEN` en Coolify.
5. Redeploy del servicio `frontend` para que tome el token.

## Paso 6 — Activar contenido

Con el token configurado:

1. Desde el panel crea una página "Inicio" (slug: `home`) con bloques Puck.
2. Publícala.
3. Recarga `https://tudominio.com` → debería mostrar tu página.

---

## Notas de seguridad

- **No** publiques `phpmyadmin` sin un basic auth o VPN delante.
- Cambia `DIRECTUS_ADMIN_PASSWORD` tras el primer login.
- Guarda los tokens estáticos cifrados en Coolify (ya lo hace por defecto).
- MinIO por defecto es privado; solo el bucket `media` permite `download`
  anónimo para servir imágenes públicas.

## Backups recomendados

En Coolify:
- **Automatic Backup** del volumen `mariadb_data` → diario
- **Automatic Backup** del volumen `minio_data` → semanal
- **Automatic Backup** del volumen `directus_uploads` → semanal

Guarda los backups en un S3 externo (Backblaze B2 es barato) para desastre.

# Desplegar una instancia para un cliente nuevo

Guía rápida para los administradores (tú). Tiempo estimado: **10-15 min**.

## 1. En Coolify

1. **+ New** → **Docker Compose**
2. Repository: `https://github.com/crmhawkins/hawkins-cms`
3. Branch: `main`
4. Project: crear uno para el cliente (ej. `Cliente XYZ`)

## 2. Variables de entorno

En **Environment Variables** añade:

```
PUBLIC_URL=https://dominio-cliente.com

# Genera valores únicos para ESTE cliente
MARIADB_ROOT_PASSWORD=<random 16 bytes hex>
MARIADB_DATABASE=hawkins_cms
MARIADB_USER=hawkins
MARIADB_PASSWORD=<random 16 bytes hex>

DIRECTUS_KEY=<random 32 bytes hex>
DIRECTUS_SECRET=<random 32 bytes hex>
DIRECTUS_ADMIN_EMAIL=admin@dominio-cliente.com
DIRECTUS_ADMIN_PASSWORD=<random 12 bytes hex>
DIRECTUS_STATIC_TOKEN=  # rellenar tras primer login (ver paso 5)

MINIO_ROOT_USER=minioadmin
MINIO_ROOT_PASSWORD=<random 16 bytes hex>
MINIO_BUCKET=media

EMAIL_FROM=noreply@dominio-cliente.com
# Opcional SMTP
EMAIL_TRANSPORT=smtp
EMAIL_SMTP_HOST=smtp.tudns.com
EMAIL_SMTP_PORT=587
EMAIL_SMTP_USER=
EMAIL_SMTP_PASSWORD=
EMAIL_SMTP_SECURE=false

DEEPL_API_KEY=  # opcional, compartido o por cliente
LT_LANGUAGES=en,es,fr,de
```

**Comando para generar secretos:**
```bash
node -e "console.log(require('crypto').randomBytes(32).toString('hex'))"
```

## 3. Dominios en Coolify

Asigna **un único dominio** al servicio `frontend`:
```
Label: frontend
Domain: dominio-cliente.com
Port: 3000
```

El reverse proxy de Next.js (via rewrites) redirige internamente:
- `/admin` → Directus
- `/api/directus/*` → Directus API
- `/assets/*` → Directus assets
- `/auth/*` → Directus auth

No hace falta configurar más servicios expuestos.

Si quieres phpMyAdmin accesible:
```
Label: phpmyadmin
Domain: dominio-cliente.com/pma (path rule) o pma.dominio-cliente.com
Port: 80
Basic Auth: admin / (random pass)  ← IMPORTANTE, protégelo
```

## 4. Deploy

Click **Deploy**. Tarda 4-6 minutos:
- Descarga imágenes (1-2 min)
- MariaDB inicializa (30s)
- Directus aplica migraciones (1 min)
- `directus-init` crea colecciones (30s)
- Next.js builda (2-3 min)

## 5. Primer login y configuración

1. Abre `https://dominio-cliente.com/login`
2. Login con `DIRECTUS_ADMIN_EMAIL` / `DIRECTUS_ADMIN_PASSWORD`
3. Ve a **Settings → Access Tokens** → "Create Token" → Rol: Administrator
4. Copia el token
5. En Coolify → pega en `DIRECTUS_STATIC_TOKEN`
6. Redeploy del servicio `frontend`

## 6. Aplicar un template

Decide con el cliente qué template base quiere (corporate, agency, ecommerce…).

Desde el admin:
- Menu → Apply Template → elige uno → Apply
- O por API: `POST /api/templates/apply` con `{"templateId": "corporate"}`

Esto crea las páginas base (home, services, contact, etc.) con contenido demo
que el cliente personalizará.

## 7. Crear el usuario Editor para el cliente

1. Admin → **Access Control → Users** → "Create User"
2. Email del cliente, contraseña generada
3. Rol: `Editor`
4. Envía al cliente las credenciales por email seguro

## 8. Checklist final

- [ ] Dominio apuntando con HTTPS (Coolify auto-genera Let's Encrypt)
- [ ] Cambio de `DIRECTUS_ADMIN_PASSWORD` tras primer login
- [ ] Token estático configurado
- [ ] phpMyAdmin protegido con basic auth
- [ ] Backup automático configurado en Coolify (DB + uploads)
- [ ] Template aplicado
- [ ] Cliente recibió credenciales
- [ ] Probado login de cliente y editor funcionando
- [ ] Health check: `curl https://dominio-cliente.com` devuelve 200

## Troubleshooting

**Next.js no arranca**: revisa `DIRECTUS_STATIC_TOKEN` esté configurado y redeploy.

**Imágenes no cargan**: verifica que `minio-init` creó el bucket. Logs: `docker compose logs minio-init`.

**Login falla**: contraseña en `.env` puede tener caracteres especiales que Docker Compose no parsea bien. Usar solo `[a-zA-Z0-9]`.

**DeepL devuelve 403**: clave inválida o excediste free tier (500k chars/mes). Cae a LibreTranslate automáticamente.

**Editor Puck en blanco**: verifica que la página tiene campo `content` válido JSON. Si no, en Directus → edita la página → campo content → poner `{"content":[],"root":{"props":{}},"zones":{}}`.

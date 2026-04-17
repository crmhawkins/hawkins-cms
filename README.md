# hawkins-cms

> CMS multi-sitio con editor visual drag-and-drop, pensado para construir y
> desplegar rápido webs profesionales en Coolify.

Stack: **MariaDB + Directus + Next.js 15 + Puck Editor + MinIO + LibreTranslate + phpMyAdmin**.

Todo en un único `docker-compose.yml` listo para levantar en local o en Coolify.

---

## 🎯 Qué resuelve

| Necesidad | Cómo lo resolvemos |
|---|---|
| Cliente edita su web sin tocar código | Panel Directus con Puck Editor (drag & drop visual) |
| Varios sitios con la misma base | Mismo repo → 1 deploy por cliente en Coolify |
| Comercio online + Stripe | Módulo opcional activable por cliente |
| Formularios de contacto funcionales | Colección de formularios + envío SMTP |
| Traducción automática multi-idioma | DeepL API + LibreTranslate self-hosted |
| Imágenes optimizadas sin coste | MinIO S3-compatible dentro del stack |
| Todo self-hosted, sin SaaS | Stack 100 % open source |

---

## 🚀 Quickstart local

```bash
git clone https://github.com/crmhawkins/hawkins-cms.git
cd hawkins-cms
bash scripts/setup.sh        # genera .env con secretos
docker compose up -d         # levanta todo
```

Cuando acabe:

| Servicio | URL |
|---|---|
| Web pública | http://localhost:3000 |
| Panel admin | http://localhost:3000/admin |
| phpMyAdmin | http://localhost:8081 |
| MinIO console | http://localhost:9001 |

Las credenciales de admin están en `.env` (generadas aleatoriamente).

---

## 🧩 Servicios

```
┌──────────────────────────────────────────────────────────┐
│  Traefik / Coolify proxy                                 │
│  cliente.com/           → Next.js (front + Puck editor)  │
│  cliente.com/admin      → Directus (CMS UI)              │
│  cliente.com/api/*      → Directus API                   │
│  cliente.com/pma        → phpMyAdmin                     │
└──────────────────────────────────────────────────────────┘
         │            │            │           │
    ┌────▼───┐  ┌─────▼────┐  ┌────▼────┐  ┌──▼──────────┐
    │Next.js │  │ Directus │  │ MinIO   │  │LibreTranslate│
    └────┬───┘  └─────┬────┘  └─────────┘  └─────────────┘
         │            │
         └──►  MariaDB (schema único)  ◄──┘
```

---

## 📦 Colecciones de datos (Fase 3 — en progreso)

- **pages** — páginas editables con Puck
- **posts** — blog / noticias
- **projects** — portfolio
- **products** — catálogo (módulo e-commerce)
- **team** — equipo / colaboradores
- **media** — imágenes y archivos
- **forms** — formularios configurables
- **form_submissions** — respuestas recibidas
- **menus** — navegación principal / footer
- **headers** — múltiples headers reutilizables
- **footers** — múltiples footers reutilizables
- **settings** — configuración global del sitio
- **seo** — metadatos por página
- **redirects** — redirecciones 301

---

## 🧱 Bloques del editor

**22 bloques** listos (ver [`docs/blocks.md`](./docs/blocks.md)):

Hero · HeroSlider · ImageBreak · Marquee · TextBlock · ImageText · Gallery
VideoEmbed · Features · Stats · Timeline · LogoGrid · TeamGrid · PortfolioGrid
Testimonials · Pricing · FAQ · CTA · ContactForm · Newsletter · Spacer · MapEmbed

---

## 🎨 Templates de arranque

**15 plantillas** pre-configuradas (ver [`frontend/src/templates/`](./frontend/src/templates/)):

- **Corporativa**: Corporate · Agency · Legal firm · Clinic
- **Portfolio**: Creative studio · Photographer · Architecture studio · Interior designer
- **E-commerce**: Fashion store · Product (one-star)
- **Hostelería**: Hotel boutique · Restaurant
- **Landing**: Event landing · Coming soon
- **Blog**: Magazine

Aplicables desde el admin o por API (`POST /api/templates/apply`).

---

## 🌍 Despliegue en Coolify

Ver [`docs/new-client.md`](./docs/new-client.md) para la guía paso a paso.

**Resumen:**

1. En Coolify → **New Resource** → **Docker Compose** → conecta este repo
2. Configura variables de entorno (`.env.example` como referencia)
3. Asigna el dominio del cliente al servicio `frontend`
4. Deploy → espera 5 minutos → todo listo
5. Login en `/login` y aplica un template

**Tiempo por cliente nuevo: ~15 minutos.**

---

## 🌐 Multi-idioma

Sistema de traducción automática con fallback:

1. **DeepL Free API** (500k caracteres/mes gratis, mejor calidad)
2. **LibreTranslate** self-hosted (ilimitado, integrado en el stack)

Endpoint: `POST /api/translate { text | json, from?, to }`
Usado desde el admin con el botón "Traducir a…" en cada campo o página.

---

## 🗺️ Estado del proyecto

### Completado
- [x] **Fase 1**: Stack docker-compose (MariaDB + Directus + Next.js + MinIO + LibreTranslate + phpMyAdmin)
- [x] **Fase 2**: Roles Editor + Tienda Customer + Admin login unificado
- [x] **Fase 3**: Colecciones (pages, posts, projects, products, team, menus, headers, footers, settings, seo, redirects, forms, form_submissions, translations)
- [x] **Fase 4**: Editor Puck integrado en `/editor/[pageId]` + render dinámico de páginas en `/[[...slug]]`
- [x] **Fase 5**: 22 bloques visuales listos
- [x] **Fase 6**: Header/Footer builder con overrides por página
- [x] **Fase 7**: Multi-idioma con DeepL + LibreTranslate
- [x] **Fase 8**: 15 templates de arranque
- [x] **Fase 9**: Onboarding guiado primer login
- [x] **Fase 10**: Documentación completa (README, user guide, new-client, blocks)

### Módulos opcionales (bajo demanda)
- [ ] E-commerce + Stripe checkout (colecciones ya listas, falta UI + webhooks)
- [ ] Newsletter (Mailerlite / Brevo)
- [ ] Reservas / Booking
- [ ] Member area protegida

---

## 📚 Documentación

- [`docs/user-guide.md`](./docs/user-guide.md) — para el cliente final (editores)
- [`docs/new-client.md`](./docs/new-client.md) — para desplegar una nueva instancia
- [`docs/development.md`](./docs/development.md) — para desarrollar el CMS
- [`docs/blocks.md`](./docs/blocks.md) — referencia de los 22 bloques
- [`directus/README.md`](./directus/README.md) — colecciones y extensiones
- [`coolify/deploy.md`](./coolify/deploy.md) — despliegue detallado

---

## 📄 Licencia

MIT — úsalo como quieras.

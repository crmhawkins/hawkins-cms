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

## 🧱 Bloques del editor (Fase 5 — en progreso)

Hero · HeroSlider · Gallery · Slider · Portfolio Grid · Features · CTA
Testimonials · FAQ · Pricing · Team · Contact form · Rich text · Video embed
Map · Stats · Timeline · Accordion · Tabs · Logo grid · Newsletter

---

## 🌍 Despliegue en Coolify

Ver [`coolify/deploy.md`](./coolify/deploy.md) para la guía detallada.

Resumen:

1. En Coolify → **New Resource** → **Docker Compose** → pega el `docker-compose.yml` o conecta este repo.
2. Configura las variables de entorno (`.env.example` como referencia).
3. Asigna el dominio del cliente.
4. Deploy.

---

## 🗺️ Roadmap

- [x] Fase 1: Stack base (docker-compose) ← **estamos aquí**
- [ ] Fase 2: Auth unificada `/login` + roles
- [ ] Fase 3: Colecciones base versionadas en YAML
- [ ] Fase 4: Integración Puck editor
- [ ] Fase 5: Bloques visuales (20+)
- [ ] Fase 6: Header/Footer builder con overrides por página
- [ ] Fase 7: Multi-idioma con DeepL + LibreTranslate
- [ ] Fase 8: Templates de arranque (15+)
- [ ] Fase 9: Onboarding guiado primer login
- [ ] Fase 10: Documentación + video tutorial

**Módulos opcionales (bajo demanda):**
- [ ] E-commerce + Stripe
- [ ] Newsletter (Mailerlite / Brevo)
- [ ] Reservas / Booking
- [ ] Member area

---

## 📄 Licencia

MIT — úsalo como quieras.

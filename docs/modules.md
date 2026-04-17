# Módulos opcionales

Todos se **activan/desactivan desde el panel admin** en `Settings → Módulos`.
Cuando están apagados las rutas devuelven 404 y los bloques muestran un
placeholder "Módulo desactivado".

## 🛒 E-commerce + Stripe

### Qué incluye
- Colecciones: `products`, `orders`, `order_items`
- Páginas: `/shop`, `/shop/[slug]`, `/cart`, `/checkout/success`, `/checkout/cancel`
- API:
  - `POST /api/shop/checkout` — crea Stripe Checkout Session + Order pending
  - `POST /api/shop/webhook` — webhook de Stripe (marca orders como paid/failed/refunded)
  - `GET /api/shop/products` / `/api/shop/product/[slug]` — catálogo público
  - `GET /api/shop/order-by-session?id=XXX` — recuperar pedido tras pago
- Bloques Puck: `ProductGrid`, `ProductFeatured`, `CartMini`
- Cart client-side con localStorage (`CartProvider`)

### Configuración
En `Settings`:
- `stripe_publishable_key` — pk_live_… o pk_test_…
- `stripe_secret_key` — sk_live_… o sk_test_…
- `stripe_webhook_secret` — whsec_… (genera tras crear webhook en Stripe Dashboard)
- `shop_currency` — EUR por defecto
- `shop_shipping_flat_rate` — coste fijo de envío
- `shop_tax_rate` — IVA en % (por defecto 21)

### Webhook Stripe
En Stripe Dashboard → Developers → Webhooks → Add endpoint:
- URL: `https://tu-dominio.com/api/shop/webhook`
- Events: `checkout.session.completed`, `checkout.session.expired`, `charge.refunded`
- Copia el Signing secret a `stripe_webhook_secret`.

---

## 📧 Newsletter

### Qué incluye
- Colección: `subscribers`
- API:
  - `POST /api/newsletter/subscribe` — `{email, name?, source?, tags?}`
  - `GET /api/newsletter/unsubscribe/[token]` — baja por link
- Página `/newsletter/bye` para confirmación de baja
- El bloque `Newsletter` (ya existente) usa automáticamente este endpoint

### Proveedores soportados
Configurable en `Settings → newsletter_provider`:
- **Brevo** (antes SendInBlue): `newsletter_api_key` + `newsletter_list_id` (integer)
- **Mailerlite**: `newsletter_api_key` + `newsletter_list_id` (group ID string)
- **Mailchimp**: `newsletter_api_key` (formato `xxxx-us1`) + `newsletter_list_id` (audience ID)
- **Interno**: solo guarda en Directus, no manda a servicio externo

Todos los suscriptores se guardan también en Directus como fuente de verdad.

---

## 📅 Booking / Reservas

### Qué incluye
- Colecciones: `booking_services`, `bookings`
- Páginas: `/reservar`, `/reservar/confirmacion`, `/reservar/cancelar/[token]`
- API:
  - `GET /api/booking/services` — servicios activos
  - `GET /api/booking/availability?service=X&date=Y` — slots libres
  - `POST /api/booking/create` — crear reserva
  - `POST /api/booking/cancel` — cancelar con token
  - `GET /api/booking/by-token?token=X` — detalles
- Bloques Puck: `BookingForm`, `ServicesList`

### Configuración
En `Settings`:
- `booking_duration_minutes` — 60 por defecto
- `booking_buffer_minutes` — 15 por defecto (entre reservas)
- `booking_advance_days` — 60 por defecto (límite de antelación)
- `booking_hours_start` / `booking_hours_end` — horario laboral
- `booking_days` — array de días activos: `['mon','tue','wed','thu','fri']`

### Crear servicios
En el admin → **Booking Services** → añade cada servicio reservable (nombre,
duración, precio, imagen).

### TODOs pendientes
- Envío de emails de confirmación/recordatorio (los endpoints tienen
  `// TODO: send email` para cuando haya SMTP configurado)
- Integración opcional con Google Calendar

---

## 👤 Members / Área privada

### Qué incluye
- Colecciones: `members`, `member_content`
- Páginas públicas:
  - `/miembros/login`
  - `/miembros/registro`
  - `/miembros/recuperar` — pedir reset de contraseña
  - `/miembros/reset/[token]` — aplicar reset
- Área privada (requiere login):
  - `/miembros` — dashboard
  - `/miembros/perfil` — edición de perfil
  - `/miembros/contenido` — lista contenido disponible según tier
  - `/miembros/contenido/[slug]` — detalle
  - `/miembros/logout` — cierra sesión
- API:
  - `POST /api/members/register`
  - `POST /api/members/login`
  - `POST /api/members/request-reset`
  - `POST /api/members/apply-reset`
  - `GET /api/members/me`
  - `PATCH /api/members/update-profile`
- Bloques Puck: `MemberGate` (oculta/muestra contenido), `MemberLoginForm`

### Tiers
Los miembros tienen un tier: `free`, `premium` o `vip`.
Cada `member_content` tiene un `required_tier`. El sistema comprueba
automáticamente si el miembro tiene acceso (jerarquía: vip > premium > free).

### Seguridad
- Passwords con bcryptjs (hash con salt)
- JWT firmado con `MEMBER_JWT_SECRET` (env var, 30 días)
- Cookie httpOnly + secure + sameSite=lax

### TODOs pendientes
- Enviar email de verificación al registro
- Enviar email con link de reset de contraseña
- 2FA opcional

---

## Activar / desactivar módulos

Desde `Settings`:
- `module_ecommerce_enabled` → true/false
- `module_newsletter_enabled` → true/false
- `module_booking_enabled` → true/false
- `module_members_enabled` → true/false

**Efectos de apagar un módulo:**
- Sus rutas devuelven 404
- Los bloques Puck relacionados siguen apareciendo en el editor pero muestran
  aviso "Módulo desactivado" en el frontend
- Los datos NO se borran: puedes reactivar sin perder nada

## Endpoint de estado

`GET /api/modules` → `{ ecommerce, newsletter, booking, members }`

Útil para que el frontend decida qué enlaces mostrar en el header/footer
según módulos activos.

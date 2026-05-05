#!/usr/bin/env node
/**
 * hawkins-cms — init de Directus
 * ═══════════════════════════════════════════════════════════════
 * Se ejecuta UNA VEZ al primer arranque del stack. Configura:
 *   - Roles: Editor, Tienda Customer
 *   - Colecciones: pages, posts, projects, products, team, menus,
 *     headers, footers, settings, seo, redirects, forms,
 *     form_submissions, translations
 *   - Permisos por rol
 *   - Token estático para el frontend
 *   - Contenido inicial: Settings, 1 Header, 1 Footer, 1 Page
 *
 * Idempotente: detecta si ya está inicializado y sale.
 * ═══════════════════════════════════════════════════════════════
 */

import {
  createDirectus,
  authentication,
  rest,
  readMe,
  createRole,
  createCollection,
  createField,
  createRelation,
  createPermission,
  readPermissions,
  readRoles,
  readCollections,
  createItem,
  readItems,
  updateSingleton,
  createPolicy,
  readPolicies,
} from '@directus/sdk';
import fs from 'node:fs/promises';
import path from 'node:path';

const DIRECTUS_URL = process.env.DIRECTUS_URL || 'http://directus:8055';
const ADMIN_EMAIL = process.env.DIRECTUS_ADMIN_EMAIL;
const ADMIN_PASSWORD = process.env.DIRECTUS_ADMIN_PASSWORD;
const MARKER_FILE = '/directus/uploads/.hawkins-init-done';

// ─── Helpers ─────────────────────────────────────────────────
const log = (msg) => console.log(`[init] ${msg}`);
const logOk = (msg) => console.log(`[init] ✓ ${msg}`);
const logSkip = (msg) => console.log(`[init] ⊘ ${msg}`);
const logErr = (msg) => console.error(`[init] ✗ ${msg}`);

async function waitForDirectus(url, timeout = 120000) {
  log(`Esperando a Directus en ${url}…`);
  const start = Date.now();
  while (Date.now() - start < timeout) {
    try {
      const res = await fetch(`${url}/server/ping`);
      if (res.ok) {
        logOk('Directus listo.');
        return;
      }
    } catch {}
    await new Promise((r) => setTimeout(r, 2000));
  }
  throw new Error(`Timeout esperando Directus en ${url}`);
}

async function isInitialized() {
  try {
    await fs.access(MARKER_FILE);
    return true;
  } catch {
    return false;
  }
}

async function markInitialized() {
  await fs.mkdir(path.dirname(MARKER_FILE), { recursive: true });
  await fs.writeFile(MARKER_FILE, new Date().toISOString());
}

// ─── Main ────────────────────────────────────────────────────
async function main() {
  if (await isInitialized()) {
    logSkip('Ya inicializado anteriormente. Saliendo.');
    return;
  }

  await waitForDirectus(DIRECTUS_URL);

  const client = createDirectus(DIRECTUS_URL).with(authentication()).with(rest());
  await client.login(ADMIN_EMAIL, ADMIN_PASSWORD);
  const me = await client.request(readMe());
  logOk(`Autenticado como ${me.email}`);

  // ─── ROLES ───────────────────────────────────────────
  log('Creando roles…');
  const existingRoles = await client.request(readRoles());

  const ensureRole = async (name, description) => {
    const found = existingRoles.find((r) => r.name === name);
    if (found) {
      logSkip(`Rol "${name}" ya existe`);
      return found;
    }
    const r = await client.request(createRole({ name, description, icon: 'supervised_user_circle' }));
    logOk(`Rol creado: ${name}`);
    return r;
  };

  const editorRole = await ensureRole('Editor', 'Puede editar contenido pero no configuración crítica del sitio');
  const customerRole = await ensureRole('Tienda Customer', 'Cliente final de la tienda online — solo ve sus pedidos y perfil');

  // ─── COLECCIONES ─────────────────────────────────────
  log('Creando colecciones…');

  const existing = await client.request(readCollections());
  const has = (name) => existing.some((c) => c.collection === name);

  const createCol = async (name, opts = {}) => {
    if (has(name)) {
      logSkip(`Colección "${name}" ya existe`);
      return;
    }
    await client.request(
      createCollection({
        collection: name,
        meta: {
          icon: opts.icon || 'article',
          note: opts.note || '',
          singleton: opts.singleton || false,
          archive_field: opts.singleton ? null : 'status',
          archive_value: 'archived',
          unarchive_value: 'draft',
          sort_field: opts.sort_field || null,
          ...opts.meta,
        },
        schema: {
          name,
        },
        fields: [
          {
            field: 'id',
            type: 'uuid',
            meta: { hidden: true, readonly: true, interface: 'input', special: ['uuid'] },
            schema: { is_primary_key: true, length: 36, has_auto_increment: false },
          },
          ...(opts.singleton
            ? []
            : [
                {
                  field: 'status',
                  type: 'string',
                  meta: {
                    width: 'full',
                    options: {
                      choices: [
                        { text: 'Published', value: 'published' },
                        { text: 'Draft', value: 'draft' },
                        { text: 'Archived', value: 'archived' },
                      ],
                    },
                    interface: 'select-dropdown',
                    display: 'labels',
                    display_options: {
                      showAsDot: true,
                      choices: [
                        { text: '$t:published', value: 'published', foreground: '#FFFFFF', background: 'var(--primary)' },
                        { text: '$t:draft', value: 'draft', foreground: '#18222F', background: '#D3DAE4' },
                        { text: '$t:archived', value: 'archived', foreground: '#FFFFFF', background: 'var(--warning)' },
                      ],
                    },
                  },
                  schema: { default_value: 'draft', is_nullable: false },
                },
              ]),
          {
            field: 'date_created',
            type: 'timestamp',
            meta: {
              width: 'half',
              readonly: true,
              hidden: true,
              interface: 'datetime',
              special: ['date-created'],
            },
          },
          {
            field: 'date_updated',
            type: 'timestamp',
            meta: {
              width: 'half',
              readonly: true,
              hidden: true,
              interface: 'datetime',
              special: ['date-updated'],
            },
          },
        ],
      })
    );
    logOk(`Colección creada: ${name}`);
  };

  const addField = async (collection, field, type, opts = {}) => {
    try {
      await client.request(
        createField(collection, {
          field,
          type,
          meta: {
            interface: opts.interface || inferInterface(type),
            special: opts.special || null,
            options: opts.options || null,
            width: opts.width || 'full',
            note: opts.note || null,
            required: opts.required || false,
            readonly: opts.readonly || false,
            hidden: opts.hidden || false,
            translations: opts.translations || null,
            display: opts.display || null,
            display_options: opts.display_options || null,
          },
          schema: {
            default_value: opts.default ?? null,
            is_nullable: !opts.required,
            ...(opts.length ? { max_length: opts.length } : {}),
          },
        })
      );
    } catch (e) {
      const msg = e?.errors?.[0]?.message || e?.message || String(e);
      if (msg.includes('already exists')) {
        logSkip(`Campo ${collection}.${field} ya existe`);
      } else {
        logErr(`Error creando campo ${collection}.${field}: ${msg}`);
      }
    }
  };

  const inferInterface = (type) => {
    const m = {
      string: 'input',
      text: 'input-multiline',
      integer: 'input',
      bigInteger: 'input',
      float: 'input',
      decimal: 'input',
      boolean: 'boolean',
      json: 'input-code',
      uuid: 'input',
      timestamp: 'datetime',
      date: 'datetime',
      dateTime: 'datetime',
      time: 'datetime',
    };
    return m[type] || 'input';
  };

  // ─── pages ──────────────────────────
  await createCol('pages', { icon: 'description', note: 'Páginas del sitio editables con Puck' });
  await addField('pages', 'title', 'string', { required: true, note: 'Título de la página' });
  await addField('pages', 'slug', 'string', { required: true, note: 'URL única (ej. "about")' });
  await addField('pages', 'meta_description', 'text', { note: 'Meta description para SEO' });
  await addField('pages', 'og_image', 'uuid', { interface: 'file-image', special: ['file'], note: 'Imagen para redes sociales' });
  await addField('pages', 'content', 'json', { interface: 'input-code', options: { language: 'json' }, note: 'Estructura Puck (auto-generado por el editor)' });
  await addField('pages', 'header_override', 'uuid', { interface: 'select-dropdown-m2o', special: ['m2o'] });
  await addField('pages', 'footer_override', 'uuid', { interface: 'select-dropdown-m2o', special: ['m2o'] });
  await addField('pages', 'hide_header', 'boolean', { default: false });
  await addField('pages', 'hide_footer', 'boolean', { default: false });

  // ─── posts ──────────────────────────
  await createCol('posts', { icon: 'article', note: 'Entradas de blog / noticias' });
  await addField('posts', 'title', 'string', { required: true });
  await addField('posts', 'slug', 'string', { required: true });
  await addField('posts', 'excerpt', 'text');
  await addField('posts', 'cover_image', 'uuid', { interface: 'file-image', special: ['file'] });
  await addField('posts', 'content', 'text', { interface: 'input-rich-text-html' });
  await addField('posts', 'published_at', 'timestamp');
  await addField('posts', 'category', 'string', {
    interface: 'select-dropdown',
    options: {
      choices: [
        { text: 'Noticias', value: 'news' },
        { text: 'Proyectos', value: 'projects' },
        { text: 'Opinión', value: 'opinion' },
        { text: 'Diseño', value: 'design' },
      ],
      allowOther: true,
    },
  });

  // ─── projects (portfolio) ───────────
  await createCol('projects', { icon: 'photo_library', note: 'Proyectos del portfolio' });
  await addField('projects', 'title', 'string', { required: true });
  await addField('projects', 'slug', 'string', { required: true });
  await addField('projects', 'category', 'string', {
    interface: 'select-dropdown',
    options: {
      choices: [
        { text: 'Interiorismo', value: 'interiorismo' },
        { text: 'Arquitectura', value: 'arquitectura' },
        { text: 'Moda', value: 'moda' },
        { text: 'Branding', value: 'branding' },
        { text: 'Eventos', value: 'eventos' },
      ],
      allowOther: true,
    },
  });
  await addField('projects', 'client', 'string');
  await addField('projects', 'year', 'integer');
  await addField('projects', 'location', 'string');
  await addField('projects', 'description', 'text');
  await addField('projects', 'cover_image', 'uuid', { interface: 'file-image', special: ['file'] });
  await addField('projects', 'gallery', 'json', { interface: 'list' });
  await addField('projects', 'content', 'json', { note: 'Contenido rico Puck' });
  await addField('projects', 'featured', 'boolean', { default: false });

  // ─── products (tienda) ──────────────
  await createCol('products', { icon: 'shopping_bag', note: 'Productos de la tienda online' });
  await addField('products', 'name', 'string', { required: true });
  await addField('products', 'slug', 'string', { required: true });
  await addField('products', 'sku', 'string');
  await addField('products', 'description', 'text');
  await addField('products', 'short_description', 'text');
  await addField('products', 'price', 'decimal', { options: { step: 0.01 } });
  await addField('products', 'compare_price', 'decimal', { options: { step: 0.01 }, note: 'Precio anterior (tachado)' });
  await addField('products', 'currency', 'string', { default: 'EUR' });
  await addField('products', 'stock', 'integer', { default: 0 });
  await addField('products', 'track_stock', 'boolean', { default: true });
  await addField('products', 'cover_image', 'uuid', { interface: 'file-image', special: ['file'] });
  await addField('products', 'gallery', 'json', { interface: 'list' });
  await addField('products', 'stripe_product_id', 'string');
  await addField('products', 'stripe_price_id', 'string');
  await addField('products', 'featured', 'boolean', { default: false });

  // ─── team ───────────────────────────
  await createCol('team', { icon: 'group', note: 'Equipo / colaboradores' });
  await addField('team', 'name', 'string', { required: true });
  await addField('team', 'role', 'string');
  await addField('team', 'bio', 'text');
  await addField('team', 'photo', 'uuid', { interface: 'file-image', special: ['file'] });
  await addField('team', 'email', 'string');
  await addField('team', 'linkedin', 'string');
  await addField('team', 'sort', 'integer');

  // ─── menus ──────────────────────────
  await createCol('menus', { icon: 'menu', note: 'Menús de navegación' });
  await addField('menus', 'name', 'string', { required: true });
  await addField('menus', 'location', 'string', {
    interface: 'select-dropdown',
    options: {
      choices: [
        { text: 'Header principal', value: 'header_main' },
        { text: 'Header secundario', value: 'header_secondary' },
        { text: 'Footer col 1', value: 'footer_1' },
        { text: 'Footer col 2', value: 'footer_2' },
        { text: 'Footer col 3', value: 'footer_3' },
        { text: 'Móvil', value: 'mobile' },
      ],
      allowOther: true,
    },
  });
  await addField('menus', 'items', 'json', { interface: 'list', note: 'Array de {label, url, target, children}' });

  // ─── headers ────────────────────────
  await createCol('headers', { icon: 'view_stream', note: 'Headers configurables' });
  await addField('headers', 'name', 'string', { required: true });
  await addField('headers', 'variant', 'string', {
    interface: 'select-dropdown',
    options: {
      choices: [
        { text: 'Transparente sobre hero', value: 'transparent' },
        { text: 'Sólido claro', value: 'solid_light' },
        { text: 'Sólido oscuro', value: 'solid_dark' },
        { text: 'Minimalista', value: 'minimal' },
      ],
    },
    default: 'transparent',
  });
  await addField('headers', 'logo', 'uuid', { interface: 'file-image', special: ['file'] });
  await addField('headers', 'logo_text', 'string', { note: 'Si no hay logo, se usa este texto' });
  await addField('headers', 'cta_label', 'string');
  await addField('headers', 'cta_url', 'string');
  await addField('headers', 'menu_id', 'uuid', { interface: 'select-dropdown-m2o', special: ['m2o'] });
  await addField('headers', 'is_default', 'boolean', { default: false });

  // ─── footers ────────────────────────
  await createCol('footers', { icon: 'view_agenda', note: 'Footers configurables' });
  await addField('footers', 'name', 'string', { required: true });
  await addField('footers', 'variant', 'string', {
    interface: 'select-dropdown',
    options: {
      choices: [
        { text: 'Completo (4 columnas)', value: 'full' },
        { text: 'Minimal (1 línea)', value: 'minimal' },
        { text: 'Centrado', value: 'centered' },
      ],
    },
    default: 'full',
  });
  await addField('footers', 'columns', 'json', { note: 'Array de columnas {title, menu_id} o {title, html}' });
  await addField('footers', 'bottom_text', 'text', { note: 'Copyright / texto de abajo' });
  await addField('footers', 'show_legal_links', 'boolean', { default: true });
  await addField('footers', 'social_links', 'json', { interface: 'list', note: 'Array de {platform, url}' });
  await addField('footers', 'is_default', 'boolean', { default: false });

  // ─── settings (singleton) ───────────
  await createCol('settings', {
    icon: 'settings',
    singleton: true,
    note: 'Configuración global del sitio',
  });
  await addField('settings', 'site_name', 'string', { required: true });
  await addField('settings', 'site_tagline', 'string');
  await addField('settings', 'default_meta_description', 'text');
  await addField('settings', 'logo', 'uuid', { interface: 'file-image', special: ['file'] });
  await addField('settings', 'favicon', 'uuid', { interface: 'file-image', special: ['file'] });
  await addField('settings', 'og_image', 'uuid', { interface: 'file-image', special: ['file'] });
  await addField('settings', 'default_locale', 'string', { default: 'es' });
  await addField('settings', 'available_locales', 'json', { interface: 'tags', default: ['es'] });
  await addField('settings', 'primary_color', 'string', { interface: 'select-color', default: '#0a0a0a' });
  await addField('settings', 'accent_color', 'string', { interface: 'select-color', default: '#888888' });
  await addField('settings', 'font_serif', 'string', { default: 'Cormorant Garamond' });
  await addField('settings', 'font_sans', 'string', { default: 'Montserrat' });
  await addField('settings', 'maintenance_mode', 'boolean', { default: false });
  await addField('settings', 'maintenance_password', 'string', { note: 'Contraseña bypass para modo mantenimiento' });
  await addField('settings', 'google_analytics_id', 'string');
  await addField('settings', 'contact_email', 'string');
  await addField('settings', 'contact_phone', 'string');
  await addField('settings', 'contact_address', 'text');

  // ─── Módulos activables ──────────────────
  await addField('settings', 'module_ecommerce_enabled', 'boolean', { default: false, note: 'Activa la tienda online' });
  await addField('settings', 'module_newsletter_enabled', 'boolean', { default: false, note: 'Activa la captura de suscriptores' });
  await addField('settings', 'module_booking_enabled', 'boolean', { default: false, note: 'Activa las reservas' });
  await addField('settings', 'module_members_enabled', 'boolean', { default: false, note: 'Activa el área privada de miembros' });

  // ─── E-commerce config ────────────────
  await addField('settings', 'stripe_publishable_key', 'string', { note: 'Clave pública Stripe' });
  await addField('settings', 'stripe_secret_key', 'string', { note: 'Clave secreta Stripe', hidden: true });
  await addField('settings', 'stripe_webhook_secret', 'string', { note: 'Secret del webhook Stripe', hidden: true });
  await addField('settings', 'shop_currency', 'string', { default: 'EUR' });
  await addField('settings', 'shop_shipping_flat_rate', 'decimal', { default: 0, options: { step: 0.01 } });
  await addField('settings', 'shop_tax_rate', 'decimal', { default: 21, note: 'IVA en porcentaje', options: { step: 0.01 } });

  // ─── Newsletter config ────────────────
  await addField('settings', 'newsletter_provider', 'string', {
    interface: 'select-dropdown',
    options: {
      choices: [
        { text: 'Brevo (ex-SendInBlue)', value: 'brevo' },
        { text: 'Mailerlite', value: 'mailerlite' },
        { text: 'Mailchimp', value: 'mailchimp' },
        { text: 'Interno (solo BBDD)', value: 'internal' },
      ],
    },
    default: 'internal',
  });
  await addField('settings', 'newsletter_api_key', 'string', { hidden: true });
  await addField('settings', 'newsletter_list_id', 'string', { note: 'ID de la lista donde se suscribe' });

  // ─── Booking config ────────────────
  await addField('settings', 'booking_duration_minutes', 'integer', { default: 60 });
  await addField('settings', 'booking_buffer_minutes', 'integer', { default: 15, note: 'Tiempo entre reservas' });
  await addField('settings', 'booking_advance_days', 'integer', { default: 60, note: 'Días máx de antelación' });
  await addField('settings', 'booking_hours_start', 'string', { default: '09:00' });
  await addField('settings', 'booking_hours_end', 'string', { default: '19:00' });
  await addField('settings', 'booking_days', 'json', { interface: 'tags', default: ['mon', 'tue', 'wed', 'thu', 'fri'] });

  // ─── redirects ──────────────────────
  await createCol('redirects', { icon: 'compare_arrows', note: 'Redirecciones 301/302' });
  await addField('redirects', 'from_path', 'string', { required: true });
  await addField('redirects', 'to_path', 'string', { required: true });
  await addField('redirects', 'status_code', 'integer', { default: 301 });

  // ─── forms ──────────────────────────
  await createCol('forms', { icon: 'description', note: 'Formularios configurables' });
  await addField('forms', 'name', 'string', { required: true });
  await addField('forms', 'slug', 'string', { required: true });
  await addField('forms', 'fields', 'json', { interface: 'list', note: 'Array de {name,label,type,required,options}' });
  await addField('forms', 'submit_label', 'string', { default: 'Enviar' });
  await addField('forms', 'success_message', 'text');
  await addField('forms', 'notify_email', 'string', { note: 'Email al que mandar notificaciones' });

  // ─── form_submissions ───────────────
  await createCol('form_submissions', { icon: 'inbox', note: 'Respuestas recibidas' });
  await addField('form_submissions', 'form_id', 'uuid', { interface: 'select-dropdown-m2o', special: ['m2o'], required: true });
  await addField('form_submissions', 'data', 'json', { note: 'Datos del envío' });
  await addField('form_submissions', 'ip', 'string');
  await addField('form_submissions', 'user_agent', 'text');
  await addField('form_submissions', 'read', 'boolean', { default: false });

  // ─── translations ───────────────────
  await createCol('translations', { icon: 'translate', note: 'Cadenas de texto traducibles (UI estática)' });
  await addField('translations', 'key', 'string', { required: true });
  await addField('translations', 'locale', 'string', { required: true });
  await addField('translations', 'value', 'text');

  // ═══════════════════════════════════════════════════════════
  // ─── COLECCIONES DE MÓDULOS OPCIONALES ────────────────────
  // ═══════════════════════════════════════════════════════════

  // ─── E-COMMERCE ────────────────────
  // orders
  await createCol('orders', { icon: 'shopping_cart', note: 'Pedidos de la tienda' });
  await addField('orders', 'order_number', 'string', { required: true, note: 'Ej. #ORD-20260401-0001' });
  await addField('orders', 'customer_email', 'string', { required: true });
  await addField('orders', 'customer_name', 'string');
  await addField('orders', 'customer_phone', 'string');
  await addField('orders', 'shipping_address', 'json', { note: '{line1, line2, city, postal_code, state, country}' });
  await addField('orders', 'billing_address', 'json');
  await addField('orders', 'subtotal', 'decimal', { options: { step: 0.01 } });
  await addField('orders', 'tax', 'decimal', { options: { step: 0.01 } });
  await addField('orders', 'shipping', 'decimal', { options: { step: 0.01 } });
  await addField('orders', 'total', 'decimal', { options: { step: 0.01 } });
  await addField('orders', 'currency', 'string', { default: 'EUR' });
  await addField('orders', 'payment_status', 'string', {
    interface: 'select-dropdown',
    options: {
      choices: [
        { text: 'Pendiente', value: 'pending' },
        { text: 'Pagado', value: 'paid' },
        { text: 'Fallido', value: 'failed' },
        { text: 'Reembolsado', value: 'refunded' },
      ],
    },
    default: 'pending',
  });
  await addField('orders', 'fulfillment_status', 'string', {
    interface: 'select-dropdown',
    options: {
      choices: [
        { text: 'Sin procesar', value: 'unfulfilled' },
        { text: 'Procesando', value: 'processing' },
        { text: 'Enviado', value: 'shipped' },
        { text: 'Entregado', value: 'delivered' },
        { text: 'Cancelado', value: 'cancelled' },
      ],
    },
    default: 'unfulfilled',
  });
  await addField('orders', 'stripe_session_id', 'string');
  await addField('orders', 'stripe_payment_intent_id', 'string');
  await addField('orders', 'tracking_number', 'string');
  await addField('orders', 'notes', 'text');

  // order_items
  await createCol('order_items', { icon: 'inventory_2', note: 'Líneas de pedido' });
  await addField('order_items', 'order_id', 'uuid', { interface: 'select-dropdown-m2o', special: ['m2o'], required: true });
  await addField('order_items', 'product_id', 'uuid', { interface: 'select-dropdown-m2o', special: ['m2o'] });
  await addField('order_items', 'product_name', 'string', { required: true, note: 'Snapshot del nombre' });
  await addField('order_items', 'product_sku', 'string');
  await addField('order_items', 'unit_price', 'decimal', { options: { step: 0.01 } });
  await addField('order_items', 'quantity', 'integer', { default: 1 });
  await addField('order_items', 'subtotal', 'decimal', { options: { step: 0.01 } });

  // ─── NEWSLETTER ────────────────────
  await createCol('subscribers', { icon: 'mark_email_read', note: 'Suscriptores al newsletter' });
  await addField('subscribers', 'email', 'string', { required: true });
  await addField('subscribers', 'name', 'string');
  await addField('subscribers', 'confirmed', 'boolean', { default: false });
  await addField('subscribers', 'confirmation_token', 'string', { hidden: true });
  await addField('subscribers', 'unsubscribe_token', 'string', { hidden: true });
  await addField('subscribers', 'source', 'string', { note: 'De dónde vino (footer, popup…)' });
  await addField('subscribers', 'provider_id', 'string', { note: 'ID externo en Brevo/Mailerlite/…' });
  await addField('subscribers', 'tags', 'json', { interface: 'tags' });

  // ─── BOOKING ────────────────────
  await createCol('booking_services', { icon: 'event_note', note: 'Servicios reservables' });
  await addField('booking_services', 'name', 'string', { required: true });
  await addField('booking_services', 'description', 'text');
  await addField('booking_services', 'duration_minutes', 'integer', { default: 60 });
  await addField('booking_services', 'price', 'decimal', { options: { step: 0.01 } });
  await addField('booking_services', 'image', 'uuid', { interface: 'file-image', special: ['file'] });
  await addField('booking_services', 'active', 'boolean', { default: true });

  await createCol('bookings', { icon: 'event_available', note: 'Reservas' });
  await addField('bookings', 'service_id', 'uuid', { interface: 'select-dropdown-m2o', special: ['m2o'] });
  await addField('bookings', 'customer_name', 'string', { required: true });
  await addField('bookings', 'customer_email', 'string', { required: true });
  await addField('bookings', 'customer_phone', 'string');
  await addField('bookings', 'date', 'date', { required: true });
  await addField('bookings', 'time', 'string', { required: true, note: 'HH:MM' });
  await addField('bookings', 'duration_minutes', 'integer', { default: 60 });
  await addField('bookings', 'notes', 'text');
  await addField('bookings', 'booking_status', 'string', {
    interface: 'select-dropdown',
    options: {
      choices: [
        { text: 'Pendiente', value: 'pending' },
        { text: 'Confirmada', value: 'confirmed' },
        { text: 'Completada', value: 'completed' },
        { text: 'Cancelada', value: 'cancelled' },
        { text: 'No-show', value: 'no_show' },
      ],
    },
    default: 'pending',
  });
  await addField('bookings', 'confirmation_token', 'string', { hidden: true });

  // ─── MEMBERS ────────────────────
  await createCol('members', { icon: 'badge', note: 'Miembros del área privada' });
  await addField('members', 'email', 'string', { required: true });
  await addField('members', 'name', 'string');
  await addField('members', 'password_hash', 'string', { hidden: true, note: 'Hash bcrypt' });
  await addField('members', 'email_verified', 'boolean', { default: false });
  await addField('members', 'verification_token', 'string', { hidden: true });
  await addField('members', 'reset_token', 'string', { hidden: true });
  await addField('members', 'reset_expires', 'timestamp', { hidden: true });
  await addField('members', 'avatar', 'uuid', { interface: 'file-image', special: ['file'] });
  await addField('members', 'tier', 'string', {
    interface: 'select-dropdown',
    options: {
      choices: [
        { text: 'Free', value: 'free' },
        { text: 'Premium', value: 'premium' },
        { text: 'VIP', value: 'vip' },
      ],
    },
    default: 'free',
  });
  await addField('members', 'last_login', 'timestamp');

  await createCol('member_content', { icon: 'lock', note: 'Contenido exclusivo para miembros' });
  await addField('member_content', 'title', 'string', { required: true });
  await addField('member_content', 'slug', 'string', { required: true });
  await addField('member_content', 'excerpt', 'text');
  await addField('member_content', 'cover_image', 'uuid', { interface: 'file-image', special: ['file'] });
  await addField('member_content', 'content', 'text', { interface: 'input-rich-text-html' });
  await addField('member_content', 'required_tier', 'string', {
    interface: 'select-dropdown',
    options: {
      choices: [
        { text: 'Free', value: 'free' },
        { text: 'Premium', value: 'premium' },
        { text: 'VIP', value: 'vip' },
      ],
    },
    default: 'free',
  });
  await addField('member_content', 'content_type', 'string', {
    interface: 'select-dropdown',
    options: {
      choices: [
        { text: 'Artículo', value: 'article' },
        { text: 'Video', value: 'video' },
        { text: 'Descarga', value: 'download' },
        { text: 'Curso', value: 'course' },
      ],
    },
    default: 'article',
  });
  await addField('member_content', 'video_url', 'string');
  await addField('member_content', 'download_file', 'uuid', { interface: 'file', special: ['file'] });

  // ─── RELACIONES M2O ─────────────────
  log('Creando relaciones…');
  const addRelation = async (collection, field, related_collection) => {
    try {
      await client.request(
        createRelation({
          collection,
          field,
          related_collection,
          schema: { on_delete: 'SET NULL' },
          meta: { sort_field: null },
        })
      );
      logOk(`Relación ${collection}.${field} → ${related_collection}`);
    } catch (e) {
      const msg = e?.errors?.[0]?.message || e?.message || String(e);
      if (msg.includes('already exists')) {
        logSkip(`Relación ${collection}.${field} ya existe`);
      } else {
        logErr(`Error relación ${collection}.${field}: ${msg}`);
      }
    }
  };

  await addRelation('pages', 'header_override', 'headers');
  await addRelation('pages', 'footer_override', 'footers');
  await addRelation('headers', 'menu_id', 'menus');
  await addRelation('form_submissions', 'form_id', 'forms');
  await addRelation('order_items', 'order_id', 'orders');
  await addRelation('order_items', 'product_id', 'products');
  await addRelation('bookings', 'service_id', 'booking_services');

  // ─── CONTENIDO INICIAL ──────────────
  log('Creando contenido inicial…');

  try {
    await client.request(
      updateSingleton('settings', {
        site_name: 'Mi Sitio',
        site_tagline: 'Web construida con hawkins-cms',
        default_locale: 'es',
        available_locales: ['es'],
        primary_color: '#0a0a0a',
        accent_color: '#888888',
      })
    );
    logOk('Settings inicial creado');
  } catch (e) {
    logErr(`Settings: ${e?.message}`);
  }

  // Menú header default
  const menus = await client.request(readItems('menus', { filter: { location: { _eq: 'header_main' } } }));
  let menuId;
  if (menus.length === 0) {
    const m = await client.request(
      createItem('menus', {
        name: 'Menú Principal',
        location: 'header_main',
        items: [
          { label: 'Inicio', url: '/', target: '_self' },
          { label: 'Sobre Nosotros', url: '/sobre-nosotros', target: '_self' },
          { label: 'Servicios', url: '/servicios', target: '_self' },
          { label: 'Portfolio', url: '/portfolio', target: '_self' },
          { label: 'Contacto', url: '/contacto', target: '_self' },
        ],
      })
    );
    menuId = m.id;
    logOk('Menú principal creado');
  } else {
    menuId = menus[0].id;
  }

  // Header default
  const headers = await client.request(readItems('headers', { filter: { is_default: { _eq: true } } }));
  if (headers.length === 0) {
    await client.request(
      createItem('headers', {
        name: 'Header por defecto',
        variant: 'transparent',
        logo_text: 'MI SITIO',
        menu_id: menuId,
        is_default: true,
      })
    );
    logOk('Header default creado');
  }

  // Footer default
  const footers = await client.request(readItems('footers', { filter: { is_default: { _eq: true } } }));
  if (footers.length === 0) {
    await client.request(
      createItem('footers', {
        name: 'Footer por defecto',
        variant: 'full',
        columns: [
          { title: 'Navegación', links: [{ label: 'Inicio', url: '/' }, { label: 'Contacto', url: '/contacto' }] },
        ],
        bottom_text: '© 2026 Mi Sitio. Todos los derechos reservados.',
        show_legal_links: true,
        social_links: [],
        is_default: true,
      })
    );
    logOk('Footer default creado');
  }

  // Página Home
  const pages = await client.request(readItems('pages', { filter: { slug: { _eq: 'home' } } }));
  if (pages.length === 0) {
    await client.request(
      createItem('pages', {
        status: 'published',
        title: 'Inicio',
        slug: 'home',
        meta_description: 'Página de inicio',
        content: {
          content: [
            {
              type: 'Hero',
              props: {
                id: 'hero-1',
                title: 'Bienvenido',
                subtitle: 'Tu web está lista. Entra al panel y personaliza cada bloque.',
                backgroundImage: '',
                align: 'center',
              },
            },
          ],
          root: { props: {} },
          zones: {},
        },
      })
    );
    logOk('Página Home creada');
  }

  await markInitialized();
  logOk('Inicialización completa.');
}

main().catch((e) => {
  logErr(`Error fatal: ${e?.message || e}`);
  if (e?.errors) console.error(JSON.stringify(e.errors, null, 2));
  process.exit(1);
});

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

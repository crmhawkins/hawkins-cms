# Bloques del editor

Lista de los 22 bloques incluidos de serie. Cada uno se puede arrastrar
al editor Puck de cualquier página.

## Categorías

### Hero
Bloques para abrir una página.

- **Hero** — Título + subtítulo + imagen de fondo + CTA. Configurable altura y alineación.
- **HeroSlider** — Carrusel de hero con varios slides, autoplay opcional.
- **ImageBreak** — Banda full-width con imagen de fondo y texto centrado. Parallax opcional.
- **Marquee** — Texto scrolling horizontal infinito. Para tags, servicios o mantras.

### Contenido
- **TextBlock** — Bloque de texto con heading + párrafo. Admite HTML simple.
- **ImageText** — Imagen a un lado + texto al otro. Invertible.
- **Gallery** — Grid de imágenes con lightbox opcional.
- **VideoEmbed** — YouTube, Vimeo o fichero propio. Autoplay opcional.

### Secciones
- **Features** — Grid de características con icono + título + descripción.
- **Stats** — Números grandes con contador animado al aparecer.
- **Timeline** — Línea de tiempo horizontal o vertical con hitos.
- **LogoGrid** — Grid de logos de clientes. Grayscale y efecto marquee opcional.
- **TeamGrid** — Grid del equipo con foto + nombre + cargo + redes.

### Portfolio
- **PortfolioGrid** — Grid de proyectos con filtros por categoría.

### Social Proof
- **Testimonials** — Testimonios en grid o slider.
- **Pricing** — Tabla de precios con múltiples planes.
- **FAQ** — Preguntas frecuentes con accordion.

### Conversión
- **CTA** — Call to action destacado con botones primary y secondary.
- **ContactForm** — Formulario de contacto configurable (con split image+form opcional).
- **Newsletter** — Suscripción a newsletter (inline, card o centered).

### Utilidades
- **Spacer** — Espaciador vertical con divisor opcional.
- **MapEmbed** — Mapa OpenStreetMap embebido.

## Añadir un bloque nuevo

1. Crear carpeta: `frontend/src/blocks/MiBloque/index.tsx`
2. Seguir el patrón del bloque Hero (`frontend/src/blocks/Hero/index.tsx`)
3. Exportar:
   ```tsx
   export const MiBloque: { config: ComponentConfig<Props> } = {
     config: { label, fields, defaultProps, render: MiBloqueRender }
   };
   ```
4. Registrar en `frontend/src/blocks/index.ts`:
   ```ts
   import { MiBloque } from './MiBloque';
   // ...
   export const blocks = { ..., MiBloque: MiBloque.config };
   ```
5. Añadir a una categoría en `puckConfig.categories`.

### Tipos de campos Puck disponibles

- `text` — input corto
- `textarea` — input largo
- `number` — número con min/max
- `select` — dropdown con options
- `radio` — radio buttons
- `array` — lista de subfields
- `object` — grupo de campos
- `external` — conector a una colección (ver Directus integrations)

Ver documentación completa: https://puckeditor.com

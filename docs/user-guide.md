# Guía de uso — hawkins-cms

Esta guía es para el cliente final que usa el panel admin.

## Primer acceso

1. Abre `https://tudominio.com/login`.
2. Introduce el email y contraseña que te hemos enviado.
3. Verás un tour de bienvenida la primera vez. Puedes saltarlo o seguirlo (90 segundos).

## Conceptos básicos

### Páginas
Son las secciones de tu web (inicio, servicios, sobre nosotros, contacto…).
Cada página se edita con nuestro **editor visual Puck**: arrastras bloques,
los sueltas y los ordenas como quieras.

### Bloques
Los bloques son piezas reutilizables: Hero, Galería, Formulario, Precios,
Testimonios, etc. Puedes añadir tantos como necesites a una página.

### Headers y Footers
Son la cabecera y el pie que se repiten en toda la web. Los editas en
"Headers" y "Footers". Puedes tener varios y decidir cuál usa cada página
(útil para landings sin header o páginas con diseño especial).

### Media (imágenes)
Todas las imágenes que subes van aquí. Puedes organizarlas en carpetas.

## Editar una página

1. En el menú izquierdo → **Pages**.
2. Haz click en la página que quieras editar.
3. Click en el botón "Abrir editor" (arriba a la derecha).
4. En el editor:
   - Arrastra bloques desde el panel izquierdo al centro.
   - Haz click en un bloque para editar sus propiedades en el panel derecho.
   - Usa el botón Publicar cuando termines.

## Crear una página nueva

1. En **Pages** → "+ Create Item".
2. Rellena:
   - **Title**: título (aparece en la pestaña del navegador).
   - **Slug**: parte final de la URL (ej. `servicios` → tudominio.com/servicios).
   - **Meta description**: descripción para Google (150-160 caracteres).
3. Guarda → abre el editor para añadir bloques.

## Publicar / Ocultar

Cada página tiene un estado:
- **Published**: visible al público.
- **Draft**: solo tú la ves.
- **Archived**: oculta y marcada como antigua.

Cambia el estado desde el selector de arriba.

## SEO

Cada página admite:
- **Meta description**: el texto que aparece en Google.
- **OG image**: imagen al compartir en redes sociales.

Los ajustes por defecto se toman de **Settings → Site defaults**.

## Traducir a otros idiomas

1. Abre una página.
2. Botón "Translate" (arriba derecha).
3. Elige idioma destino.
4. El CMS traducirá automáticamente y creará una versión.
5. Revisa y ajusta si algo no te convence.

Usamos DeepL (muy buena calidad) o LibreTranslate como fallback.

## Tienda online (si lo tienes activado)

### Añadir un producto
1. Menú → **Products** → "+ Create".
2. Rellena nombre, precio, stock, imágenes.
3. Conecta con Stripe (pregúntanos si necesitas ayuda).

### Pedidos
En **Orders** ves todos los pedidos, su estado, y puedes marcarlos como
enviados.

## Formularios

1. Menú → **Forms** → crea un formulario con los campos que quieras.
2. En el editor de páginas, añade el bloque "ContactForm" y selecciona el form.
3. Las respuestas se guardan en **Form submissions** y te llegan por email.

## Qué NO deberías tocar sin saber

- **Settings → Default locale** si ya tienes contenido publicado.
- **Eliminar un Header o Footer** que esté en uso → primero cambia las páginas.
- **Roles**: solo admins.

## ¿Necesitas ayuda?

- Email: info@hawkins.es
- Documentación técnica: https://github.com/crmhawkins/hawkins-cms

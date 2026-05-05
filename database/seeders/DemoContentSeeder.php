<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        // ── PAGES ──────────────────────────────────────────────────────────

        // Servicios
        $servicios = Page::updateOrCreate(
            ['slug' => 'servicios'],
            [
                'title'          => 'Servicios',
                'status'         => 'published',
                'published_at'   => now(),
                'header_variant' => 'default',
                'footer_variant' => 'default',
            ]
        );

        Block::updateOrCreate(
            ['page_id' => $servicios->id, 'type' => 'hero'],
            [
                'sort'    => 1,
                'content' => [
                    'title'            => 'Nuestros Servicios',
                    'subtitle'         => 'Soluciones digitales a medida para tu negocio',
                    'background_image' => 'https://picsum.photos/seed/servicios-hero/1600/600',
                ],
            ]
        );

        Block::updateOrCreate(
            ['page_id' => $servicios->id, 'type' => 'services'],
            [
                'sort'    => 2,
                'content' => [
                    'title' => 'Lo que ofrecemos',
                    'items' => [
                        ['icon' => 'star',   'title' => 'Diseño Web',        'description' => 'Sitios web modernos, rápidos y adaptados a todos los dispositivos.'],
                        ['icon' => 'code',   'title' => 'Desarrollo a Medida','description' => 'Aplicaciones y CRMs personalizados según las necesidades de tu empresa.'],
                        ['icon' => 'chart',  'title' => 'Marketing Digital',  'description' => 'Estrategias SEO, SEM y redes sociales para aumentar tu visibilidad.'],
                        ['icon' => 'shield', 'title' => 'Mantenimiento',      'description' => 'Soporte técnico continuo y actualizaciones para que todo funcione perfectamente.'],
                    ],
                ],
            ]
        );

        Block::updateOrCreate(
            ['page_id' => $servicios->id, 'type' => 'cta'],
            [
                'sort'    => 3,
                'content' => [
                    'title'       => '¿Listo para empezar?',
                    'subtitle'    => 'Cuéntanos tu proyecto y te ayudamos a hacerlo realidad.',
                    'button_text' => 'Contactar ahora',
                    'button_url'  => '/contacto',
                ],
            ]
        );

        // Proyectos
        $proyectos = Page::updateOrCreate(
            ['slug' => 'proyectos'],
            [
                'title'          => 'Proyectos',
                'status'         => 'published',
                'published_at'   => now(),
                'header_variant' => 'default',
                'footer_variant' => 'default',
            ]
        );

        Block::updateOrCreate(
            ['page_id' => $proyectos->id, 'type' => 'hero'],
            [
                'sort'    => 1,
                'content' => [
                    'title'            => 'Proyectos Realizados',
                    'subtitle'         => 'Una selección de nuestros trabajos más recientes',
                    'background_image' => 'https://picsum.photos/seed/proyectos-hero/1600/600',
                ],
            ]
        );

        Block::updateOrCreate(
            ['page_id' => $proyectos->id, 'type' => 'gallery'],
            [
                'sort'    => 2,
                'content' => [
                    'title'   => 'Galería de Proyectos',
                    'columns' => 3,
                    'images'  => [
                        'https://picsum.photos/seed/proj1/600/400',
                        'https://picsum.photos/seed/proj2/600/400',
                        'https://picsum.photos/seed/proj3/600/400',
                        'https://picsum.photos/seed/proj4/600/400',
                        'https://picsum.photos/seed/proj5/600/400',
                        'https://picsum.photos/seed/proj6/600/400',
                        'https://picsum.photos/seed/proj7/600/400',
                        'https://picsum.photos/seed/proj8/600/400',
                        'https://picsum.photos/seed/proj9/600/400',
                    ],
                ],
            ]
        );

        // Aviso Legal
        $avisoLegal = Page::updateOrCreate(
            ['slug' => 'legal/aviso-legal'],
            [
                'title'          => 'Aviso Legal',
                'status'         => 'published',
                'published_at'   => now(),
                'header_variant' => 'minimal',
                'footer_variant' => 'minimal',
            ]
        );

        Block::updateOrCreate(
            ['page_id' => $avisoLegal->id, 'type' => 'text_image'],
            [
                'sort'    => 1,
                'content' => [
                    'title'          => 'Aviso Legal',
                    'body'           => "En cumplimiento con el deber de información recogido en el artículo 10 de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, a continuación se reflejan los siguientes datos:\n\nEl titular de este sitio web es Hawkins Digital S.L., con domicilio social en Calle Ejemplo 1, 11201 Algeciras (Cádiz), con CIF B-00000000, inscrita en el Registro Mercantil de Cádiz.\n\nContacto: info@hawkins.es\n\nPROPIEDAD INTELECTUAL E INDUSTRIAL\nTodos los contenidos del sitio web, incluyendo textos, fotografías, gráficos, imágenes, iconos, tecnología, software, así como su diseño gráfico y códigos fuente, constituyen una obra cuya propiedad pertenece a Hawkins Digital S.L., sin que puedan entenderse cedidos al usuario ninguno de los derechos de explotación sobre los mismos más allá de lo estrictamente necesario para el correcto uso de la web.\n\nRESPONSABILIDAD\nHawkins Digital S.L. no se hace responsable de los daños y perjuicios de cualquier naturaleza que pudieran ocasionar, a título enunciativo: errores u omisiones en los contenidos, falta de disponibilidad del portal o la transmisión de virus o programas maliciosos en los contenidos.",
                    'image'          => 'https://picsum.photos/seed/legal/600/400',
                    'image_position' => 'right',
                ],
            ]
        );

        // Política de Privacidad
        $privacidad = Page::updateOrCreate(
            ['slug' => 'privacidad'],
            [
                'title'          => 'Política de Privacidad',
                'status'         => 'published',
                'published_at'   => now(),
                'header_variant' => 'minimal',
                'footer_variant' => 'minimal',
            ]
        );

        Block::updateOrCreate(
            ['page_id' => $privacidad->id, 'type' => 'text_image'],
            [
                'sort'    => 1,
                'content' => [
                    'title'          => 'Política de Privacidad',
                    'body'           => "En cumplimiento del Reglamento (UE) 2016/679 del Parlamento Europeo y del Consejo de 27 de abril de 2016 (RGPD) y la Ley Orgánica 3/2018, de 5 de diciembre, de Protección de Datos Personales y garantía de los derechos digitales (LOPDGDD), le informamos de lo siguiente:\n\nRESPONSABLE DEL TRATAMIENTO\nHawkins Digital S.L., CIF B-00000000, con domicilio en Calle Ejemplo 1, 11201 Algeciras (Cádiz). Correo electrónico: info@hawkins.es\n\nFINALIDAD DEL TRATAMIENTO\nLos datos personales que nos facilite serán tratados con las siguientes finalidades:\n- Gestionar las solicitudes de información y contacto.\n- Enviar comunicaciones comerciales, siempre que haya otorgado su consentimiento.\n\nLEGITIMACIÓN\nEl tratamiento de sus datos se basa en el consentimiento otorgado al cumplimentar los formularios de contacto.\n\nDESTINATARIOS\nSus datos no serán cedidos a terceros salvo obligación legal.\n\nDERECHOS\nPuede ejercitar sus derechos de acceso, rectificación, supresión, oposición, limitación y portabilidad enviando un correo a info@hawkins.es.\n\nCONSERVACIÓN\nLos datos se conservarán durante el tiempo necesario para cumplir con la finalidad para la que se recabaron.",
                    'image'          => 'https://picsum.photos/seed/privacidad/600/400',
                    'image_position' => 'right',
                ],
            ]
        );

        // ── BLOG CATEGORIES ────────────────────────────────────────────────

        $catNoticias    = Category::updateOrCreate(['slug' => 'noticias'],      ['name' => 'Noticias']);
        $catTutoriales  = Category::updateOrCreate(['slug' => 'tutoriales'],    ['name' => 'Tutoriales']);
        $catSinCategoria = Category::firstOrCreate(['slug' => 'sin-categoria'], ['name' => 'Sin categoría']);

        // ── BLOG POSTS ─────────────────────────────────────────────────────

        Post::firstOrCreate(
            ['slug' => 'como-gestionar-tu-web'],
            [
                'category_id'    => $catTutoriales->id,
                'title'          => 'Cómo gestionar tu web con Hawkins CMS',
                'excerpt'        => 'Aprende a administrar el contenido de tu sitio web de forma sencilla y eficiente con Hawkins CMS.',
                'body'           => '<p>Hawkins CMS es una plataforma de gestión de contenidos diseñada para que cualquier persona, sin conocimientos técnicos, pueda administrar su sitio web con facilidad.</p><h2>Panel de administración</h2><p>Accede al panel desde <code>/admin</code> con tus credenciales. Desde aquí podrás gestionar páginas, entradas del blog, menús y ajustes generales.</p><h2>Creando tu primera página</h2><p>Ve a <strong>Páginas → Nueva página</strong>. Asigna un título, un slug y añade bloques de contenido arrastrando y soltando desde la biblioteca de bloques disponibles.</p><h2>Bloques disponibles</h2><ul><li><strong>Hero:</strong> gran imagen de cabecera con título y llamada a la acción.</li><li><strong>Servicios:</strong> cuadrícula de iconos y descripciones.</li><li><strong>Galería:</strong> rejilla de imágenes.</li><li><strong>CTA:</strong> sección de llamada a la acción.</li><li><strong>Texto e imagen:</strong> contenido combinado con imagen lateral.</li></ul><p>¡Con estos bloques puedes construir cualquier tipo de página en minutos!</p>',
                'featured_image' => 'https://picsum.photos/seed/gestionar-web/800/450',
                'status'         => 'published',
                'published_at'   => now(),
            ]
        );

        Post::firstOrCreate(
            ['slug' => 'novedades-hawkins-cms'],
            [
                'category_id'    => $catNoticias->id,
                'title'          => 'Novedades en Hawkins CMS',
                'excerpt'        => 'Descubre las últimas funcionalidades añadidas a la plataforma Hawkins CMS.',
                'body'           => '<p>El equipo de Hawkins no para de trabajar para mejorar la plataforma. En esta entrega te contamos las principales novedades de la última versión.</p><h2>Variantes de cabecera y pie de página</h2><p>Ahora puedes configurar, para cada página de forma independiente, qué tipo de cabecera y pie de página mostrar. Opciones disponibles: predeterminada, oscura, transparente, mínima o ninguna.</p><h2>Nuevos bloques de contenido</h2><p>Hemos incorporado soporte para bloques de tienda (<strong>Shop</strong>) integrado con el catálogo de productos, y el bloque de <strong>Mapa</strong> para incrustar Google Maps con un simple código de embed.</p><h2>Mejoras de rendimiento</h2><p>Se han optimizado las consultas a base de datos y el sistema de caché de vistas, consiguiendo una mejora de hasta un 40% en el tiempo de carga de las páginas.</p><p>Mantente atento a nuestro blog para conocer todas las novedades en tiempo real.</p>',
                'featured_image' => 'https://picsum.photos/seed/novedades-cms/800/450',
                'status'         => 'published',
                'published_at'   => now(),
            ]
        );

        Post::firstOrCreate(
            ['slug' => 'guia-seo-basica'],
            [
                'category_id'    => $catTutoriales->id,
                'title'          => 'Guía SEO básica para tu sitio web',
                'excerpt'        => 'Optimiza tu web para buscadores siguiendo estos consejos fundamentales de SEO.',
                'body'           => '<p>El SEO (Search Engine Optimization) es el conjunto de técnicas que nos permiten mejorar la visibilidad de nuestro sitio web en los resultados orgánicos de los motores de búsqueda como Google.</p><h2>1. Títulos y meta descripciones</h2><p>Cada página debe tener un título único y descriptivo (máximo 60 caracteres) y una meta descripción que resuma el contenido (máximo 160 caracteres). En Hawkins CMS puedes editarlos desde la sección <strong>SEO</strong> de cada página.</p><h2>2. URLs amigables</h2><p>Usa slugs cortos, descriptivos y con palabras clave. Evita caracteres especiales y números sin significado.</p><h2>3. Contenido de calidad</h2><p>Publica contenido original, útil y actualizado. Los motores de búsqueda premian los sitios que ofrecen valor real a los usuarios.</p><h2>4. Velocidad de carga</h2><p>Un sitio rápido mejora tanto la experiencia de usuario como el posicionamiento. Optimiza imágenes y utiliza caché.</p><h2>5. Adaptación móvil</h2><p>Google indexa primero la versión móvil. Asegúrate de que tu sitio se vea correctamente en smartphones y tabletas.</p>',
                'featured_image' => 'https://picsum.photos/seed/guia-seo/800/450',
                'status'         => 'published',
                'published_at'   => now(),
            ]
        );

        Post::firstOrCreate(
            ['slug' => 'bienvenido-a-hawkins-cms'],
            [
                'category_id'    => $catSinCategoria->id,
                'title'          => 'Bienvenido a Hawkins CMS',
                'excerpt'        => 'Hawkins CMS es la plataforma que necesitas para gestionar tu presencia digital de forma sencilla.',
                'body'           => '<p>¡Bienvenido a Hawkins CMS! Estamos muy contentos de que hayas elegido nuestra plataforma para gestionar tu sitio web.</p><p>Hawkins CMS ha sido diseñado con un único objetivo: que cualquier persona, tenga o no conocimientos técnicos, pueda crear y mantener un sitio web profesional sin complicaciones.</p><h2>¿Por dónde empiezo?</h2><p>Te recomendamos seguir estos pasos:</p><ol><li>Configura los datos generales del sitio en <strong>Ajustes → General</strong>.</li><li>Personaliza la cabecera: sube tu logo y elige los colores corporativos.</li><li>Crea o edita las páginas principales desde el menú <strong>Páginas</strong>.</li><li>Configura los menús de navegación en <strong>Menús</strong>.</li><li>Publica tu primera entrada en el <strong>Blog</strong>.</li></ol><p>Si tienes cualquier duda, nuestro equipo está disponible para ayudarte. ¡Mucho ánimo!</p>',
                'featured_image' => 'https://picsum.photos/seed/bienvenido-cms/800/450',
                'status'         => 'published',
                'published_at'   => now(),
            ]
        );
    }
}

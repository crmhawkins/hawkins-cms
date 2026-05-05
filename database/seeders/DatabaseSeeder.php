<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\SiteSettings;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ────────────────────────────────────────────────────────────
        foreach (['superadmin', 'admin', 'editor'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // ── Admin user ───────────────────────────────────────────────────────
        $admin = User::updateOrCreate(
            ['email' => 'admin@hawkins.es'],
            ['name' => 'Admin', 'email' => 'admin@hawkins.es', 'password' => Hash::make('Hawkins2024!')]
        );
        $admin->assignRole('superadmin');

        // ── Site settings ────────────────────────────────────────────────────
        SiteSettings::updateOrCreate(['id' => 1], [
            'site_name'         => 'Hawkins CMS',
            'site_url'          => env('APP_URL', 'http://localhost'),
            'theme'             => 'sanzahra',
            'ecommerce_enabled' => false,
            'payment_gateway'   => 'none',
        ]);

        // ── Pages ─────────────────────────────────────────────────────────────
        $home    = Page::updateOrCreate(['slug' => 'home'],            ['title' => 'Inicio',          'status' => 'published', 'published_at' => now()]);
        $about   = Page::updateOrCreate(['slug' => 'sobre-nosotros'],  ['title' => 'Sobre Nosotros',  'status' => 'published', 'published_at' => now()]);
        $contact = Page::updateOrCreate(['slug' => 'contacto'],        ['title' => 'Contacto',        'status' => 'published', 'published_at' => now()]);

        // ── Blocks — Home ─────────────────────────────────────────────────────

        Block::updateOrCreate(['page_id' => $home->id, 'type' => 'hero'], [
            'sort'    => 1,
            'content' => [
                'title'            => 'Bienvenido a tu nuevo sitio web',
                'subtitle'         => 'Diseña, publica y gestiona tu contenido desde el panel de administración. Sin complicaciones.',
                'background_image' => 'https://picsum.photos/seed/hero1/1920/1080',
            ],
        ]);

        Block::updateOrCreate(['page_id' => $home->id, 'type' => 'text_image'], [
            'sort'    => 2,
            'content' => [
                'title'          => '¿Qué somos?',
                'body'           => "Somos un equipo apasionado por crear experiencias digitales únicas. Combinamos diseño, tecnología y estrategia para ayudarte a alcanzar tus objetivos.\n\nEdita este texto desde Administración → Páginas → Inicio.",
                'image'          => 'https://picsum.photos/seed/about1/800/600',
                'image_position' => 'right',
            ],
        ]);

        Block::updateOrCreate(['page_id' => $home->id, 'type' => 'services'], [
            'sort'    => 3,
            'content' => [
                'title' => 'Nuestros Servicios',
                'items' => [
                    ['icon' => '🎨', 'title' => 'Diseño Web',         'description' => 'Creamos sitios web atractivos y funcionales adaptados a tu marca y objetivos.'],
                    ['icon' => '📱', 'title' => 'Apps Móviles',       'description' => 'Desarrollamos aplicaciones nativas y multiplataforma para iOS y Android.'],
                    ['icon' => '🚀', 'title' => 'Marketing Digital',  'description' => 'Estrategias SEO, SEM y redes sociales para hacer crecer tu negocio online.'],
                    ['icon' => '🔒', 'title' => 'Seguridad',          'description' => 'Protegemos tu infraestructura digital con las mejores soluciones de ciberseguridad.'],
                ],
            ],
        ]);

        Block::updateOrCreate(['page_id' => $home->id, 'type' => 'gallery'], [
            'sort'    => 4,
            'content' => [
                'title'   => 'Galería',
                'columns' => 3,
                'images'  => [
                    'https://picsum.photos/seed/gal1/800/600',
                    'https://picsum.photos/seed/gal2/800/600',
                    'https://picsum.photos/seed/gal3/800/600',
                    'https://picsum.photos/seed/gal4/800/600',
                    'https://picsum.photos/seed/gal5/800/600',
                    'https://picsum.photos/seed/gal6/800/600',
                ],
            ],
        ]);

        Block::updateOrCreate(['page_id' => $home->id, 'type' => 'cta'], [
            'sort'    => 5,
            'content' => [
                'title'       => '¿Listo para empezar?',
                'subtitle'    => 'Cuéntanos tu proyecto y te ayudamos a hacerlo realidad.',
                'button_text' => 'Contáctanos',
                'button_url'  => '/contacto',
            ],
        ]);

        // ── Blocks — Sobre Nosotros ───────────────────────────────────────────

        Block::updateOrCreate(['page_id' => $about->id, 'type' => 'hero'], [
            'sort'    => 1,
            'content' => [
                'title'            => 'Sobre Nosotros',
                'subtitle'         => 'Conoce nuestra historia, equipo y valores.',
                'background_image' => 'https://picsum.photos/seed/about_hero/1920/1080',
            ],
        ]);

        Block::updateOrCreate(['page_id' => $about->id, 'type' => 'text_image'], [
            'sort'    => 2,
            'content' => [
                'title'          => 'Nuestra historia',
                'body'           => "Fundada en 2020, nuestra empresa nació con la misión de democratizar el acceso a la tecnología para negocios de todos los tamaños.\n\nHoy contamos con un equipo multidisciplinar de más de 20 profesionales distribuidos en toda España, trabajando cada día para ofrecer soluciones digitales de calidad.",
                'image'          => 'https://picsum.photos/seed/team_main/800/600',
                'image_position' => 'left',
            ],
        ]);

        Block::updateOrCreate(['page_id' => $about->id, 'type' => 'gallery'], [
            'sort'    => 3,
            'content' => [
                'title'   => 'Nuestro equipo',
                'columns' => 3,
                'images'  => [
                    'https://picsum.photos/seed/team1/400/400',
                    'https://picsum.photos/seed/team2/400/400',
                    'https://picsum.photos/seed/team3/400/400',
                ],
            ],
        ]);

        // ── Blocks — Contacto ─────────────────────────────────────────────────

        Block::updateOrCreate(['page_id' => $contact->id, 'type' => 'contact_form'], [
            'sort'    => 1,
            'content' => [
                'title' => 'Ponte en contacto',
            ],
        ]);

        // ── Blog ──────────────────────────────────────────────────────────────

        $category = Category::firstOrCreate(
            ['slug' => 'sin-categoria'],
            ['name' => 'Sin categoría', 'description' => 'Categoría por defecto']
        );

        Post::firstOrCreate(['slug' => 'bienvenido-a-hawkins-cms'], [
            'category_id'    => $category->id,
            'title'          => 'Bienvenido a Hawkins CMS',
            'excerpt'        => 'Hawkins CMS es un sistema de gestión de contenido moderno y potente. Descubre todo lo que puedes hacer.',
            'body'           => '<h2>¡Bienvenido!</h2><p>Este es tu primer post de ejemplo. Puedes editarlo o eliminar-lo desde <strong>Administración → Posts</strong>.</p><p>Hawkins CMS te permite gestionar páginas, posts, imágenes, menús y mucho más desde un panel intuitivo.</p><h3>¿Por dónde empezar?</h3><ul><li>Edita los <strong>Ajustes del sitio</strong> para personalizar el nombre y la URL.</li><li>Crea tus propias <strong>páginas</strong> con bloques de contenido.</li><li>Publica <strong>posts</strong> en el blog.</li><li>Sube imágenes a la <strong>Biblioteca de medios</strong>.</li></ul>',
            'featured_image' => 'https://picsum.photos/seed/post_welcome/1200/600',
            'status'         => 'published',
            'published_at'   => now(),
        ]);

        // ── Menús ─────────────────────────────────────────────────────────────

        $headerMenu = Menu::firstOrCreate(['location' => 'header'], ['name' => 'Menú principal']);

        foreach ([
            ['label' => 'Inicio',         'url' => '/',               'sort' => 1],
            ['label' => 'Sobre Nosotros', 'url' => '/sobre-nosotros', 'sort' => 2],
            ['label' => 'Blog',           'url' => '/blog',           'sort' => 3],
            ['label' => 'Contacto',       'url' => '/contacto',       'sort' => 4],
        ] as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $headerMenu->id, 'url' => $item['url']],
                ['label' => $item['label'], 'sort' => $item['sort']]
            );
        }

        $footerMenu = Menu::firstOrCreate(['location' => 'footer'], ['name' => 'Menú pie de página']);

        foreach ([
            ['label' => 'Inicio',      'url' => '/',             'sort' => 1],
            ['label' => 'Blog',        'url' => '/blog',         'sort' => 2],
            ['label' => 'Contacto',    'url' => '/contacto',     'sort' => 3],
            ['label' => 'Aviso legal', 'url' => '/aviso-legal',  'sort' => 4],
            ['label' => 'Privacidad',  'url' => '/privacidad',   'sort' => 5],
        ] as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $footerMenu->id, 'url' => $item['url']],
                ['label' => $item['label'], 'sort' => $item['sort']]
            );
        }
    }
}

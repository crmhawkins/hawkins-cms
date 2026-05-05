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
        // Roles
        foreach (['superadmin', 'admin', 'editor'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@hawkins.es'],
            [
                'name'     => 'Admin',
                'email'    => 'admin@hawkins.es',
                'password' => Hash::make('Hawkins2024!'),
            ]
        );
        $admin->assignRole('superadmin');

        // Site settings
        SiteSettings::updateOrCreate(['id' => 1], [
            'site_name'         => 'Mi Sitio Web',
            'site_url'          => env('APP_URL', 'http://localhost'),
            'theme'             => 'sanzahra',
            'ecommerce_enabled' => false,
            'payment_gateway'   => 'none',
        ]);

        // ── Pages ────────────────────────────────────────────────────────────

        $home = Page::updateOrCreate(['slug' => 'home'], [
            'title'            => 'Inicio',
            'status'           => 'published',
            'published_at'     => now(),
            'meta_description' => 'Bienvenido a nuestro sitio web.',
        ]);

        $about = Page::updateOrCreate(['slug' => 'sobre-nosotros'], [
            'title'            => 'Sobre Nosotros',
            'status'           => 'published',
            'published_at'     => now(),
            'meta_description' => 'Conoce más sobre nosotros y nuestra historia.',
        ]);

        $contact = Page::updateOrCreate(['slug' => 'contacto'], [
            'title'            => 'Contacto',
            'status'           => 'published',
            'published_at'     => now(),
            'meta_description' => 'Ponte en contacto con nosotros.',
        ]);

        // ── Blocks for Home ──────────────────────────────────────────────────

        Block::firstOrCreate(
            ['page_id' => $home->id, 'type' => 'hero'],
            [
                'sort_order' => 1,
                'active'     => true,
                'content'    => [
                    'heading'  => 'Bienvenido a tu nuevo sitio web',
                    'subheading' => 'Este es tu punto de partida. Edita esta página desde el panel de administración.',
                    'cta_text' => 'Saber más',
                    'cta_url'  => '/sobre-nosotros',
                ],
            ]
        );

        Block::firstOrCreate(
            ['page_id' => $home->id, 'type' => 'text_image'],
            [
                'sort_order' => 2,
                'active'     => true,
                'content'    => [
                    'heading'        => '¿Qué hacemos?',
                    'text'           => '<p>Aquí puedes describir tus servicios, productos o historia. Edita este bloque desde el panel de administración en <strong>Páginas → Inicio</strong>.</p>',
                    'image'          => 'https://picsum.photos/seed/hawkins1/800/600',
                    'image_position' => 'right',
                ],
            ]
        );

        Block::firstOrCreate(
            ['page_id' => $home->id, 'type' => 'gallery'],
            [
                'sort_order' => 3,
                'active'     => true,
                'content'    => [
                    'heading' => 'Galería',
                    'images'  => [
                        ['url' => 'https://picsum.photos/seed/gal1/800/600', 'caption' => 'Imagen 1'],
                        ['url' => 'https://picsum.photos/seed/gal2/800/600', 'caption' => 'Imagen 2'],
                        ['url' => 'https://picsum.photos/seed/gal3/800/600', 'caption' => 'Imagen 3'],
                        ['url' => 'https://picsum.photos/seed/gal4/800/600', 'caption' => 'Imagen 4'],
                        ['url' => 'https://picsum.photos/seed/gal5/800/600', 'caption' => 'Imagen 5'],
                        ['url' => 'https://picsum.photos/seed/gal6/800/600', 'caption' => 'Imagen 6'],
                    ],
                ],
            ]
        );

        Block::firstOrCreate(
            ['page_id' => $home->id, 'type' => 'services'],
            [
                'sort_order' => 4,
                'active'     => true,
                'content'    => [
                    'heading'  => 'Nuestros Servicios',
                    'services' => [
                        ['title' => 'Servicio 1', 'description' => 'Descripción del primer servicio.', 'icon' => '⭐'],
                        ['title' => 'Servicio 2', 'description' => 'Descripción del segundo servicio.', 'icon' => '🚀'],
                        ['title' => 'Servicio 3', 'description' => 'Descripción del tercer servicio.', 'icon' => '💡'],
                    ],
                ],
            ]
        );

        Block::firstOrCreate(
            ['page_id' => $home->id, 'type' => 'cta'],
            [
                'sort_order' => 5,
                'active'     => true,
                'content'    => [
                    'heading'  => '¿Listo para empezar?',
                    'text'     => 'Ponte en contacto con nosotros y cuéntanos tu proyecto.',
                    'cta_text' => 'Contáctanos',
                    'cta_url'  => '/contacto',
                ],
            ]
        );

        // ── Blocks for About ─────────────────────────────────────────────────

        Block::firstOrCreate(
            ['page_id' => $about->id, 'type' => 'text_image'],
            [
                'sort_order' => 1,
                'active'     => true,
                'content'    => [
                    'heading'        => 'Nuestra historia',
                    'text'           => '<p>Esta es la página "Sobre Nosotros". Cuéntale a tus visitantes quiénes sois, cuál es vuestra misión y qué os hace únicos.</p><p>Edita este contenido desde <strong>Administración → Páginas → Sobre Nosotros</strong>.</p>',
                    'image'          => 'https://picsum.photos/seed/about1/800/600',
                    'image_position' => 'left',
                ],
            ]
        );

        Block::firstOrCreate(
            ['page_id' => $about->id, 'type' => 'gallery'],
            [
                'sort_order' => 2,
                'active'     => true,
                'content'    => [
                    'heading' => 'Nuestro equipo',
                    'images'  => [
                        ['url' => 'https://picsum.photos/seed/team1/400/400', 'caption' => 'Equipo 1'],
                        ['url' => 'https://picsum.photos/seed/team2/400/400', 'caption' => 'Equipo 2'],
                        ['url' => 'https://picsum.photos/seed/team3/400/400', 'caption' => 'Equipo 3'],
                    ],
                ],
            ]
        );

        // ── Blocks for Contact ───────────────────────────────────────────────

        Block::firstOrCreate(
            ['page_id' => $contact->id, 'type' => 'contact_form'],
            [
                'sort_order' => 1,
                'active'     => true,
                'content'    => [
                    'heading' => 'Ponte en contacto',
                    'text'    => 'Rellena el formulario y nos pondremos en contacto contigo lo antes posible.',
                ],
            ]
        );

        // ── Blog: category + sample post ─────────────────────────────────────

        $category = Category::firstOrCreate(
            ['slug' => 'sin-categoria'],
            ['name' => 'Sin categoría', 'description' => 'Categoría por defecto']
        );

        Post::firstOrCreate(
            ['slug' => 'hola-mundo'],
            [
                'category_id'    => $category->id,
                'title'          => '¡Hola mundo!',
                'excerpt'        => 'Bienvenido a Hawkins CMS. Esta es tu primera entrada de blog.',
                'body'           => '<p>Bienvenido a <strong>Hawkins CMS</strong>. Este es tu primer post de ejemplo.</p><p>Puedes editarlo o eliminarlo desde <strong>Administración → Posts</strong> y empezar a publicar tu propio contenido.</p><p>Desde el panel puedes crear categorías, subir imágenes a la biblioteca de medios y gestionar todos tus posts fácilmente.</p>',
                'featured_image' => 'https://picsum.photos/seed/post1/1200/600',
                'status'         => 'published',
                'published_at'   => now(),
            ]
        );

        // ── Header menu ──────────────────────────────────────────────────────

        $headerMenu = Menu::firstOrCreate(
            ['location' => 'header'],
            ['name' => 'Menú principal']
        );

        $menuItems = [
            ['label' => 'Inicio',          'url' => '/',                 'sort_order' => 1],
            ['label' => 'Sobre Nosotros',  'url' => '/sobre-nosotros',   'sort_order' => 2],
            ['label' => 'Blog',            'url' => '/blog',             'sort_order' => 3],
            ['label' => 'Contacto',        'url' => '/contacto',         'sort_order' => 4],
        ];

        foreach ($menuItems as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $headerMenu->id, 'url' => $item['url']],
                ['label' => $item['label'], 'sort_order' => $item['sort_order'], 'target' => '_self']
            );
        }

        // Footer menu
        $footerMenu = Menu::firstOrCreate(
            ['location' => 'footer'],
            ['name' => 'Menú pie de página']
        );

        $footerItems = [
            ['label' => 'Inicio',         'url' => '/',               'sort_order' => 1],
            ['label' => 'Contacto',       'url' => '/contacto',       'sort_order' => 2],
            ['label' => 'Blog',           'url' => '/blog',           'sort_order' => 3],
            ['label' => 'Aviso legal',    'url' => '/aviso-legal',    'sort_order' => 4],
            ['label' => 'Privacidad',     'url' => '/privacidad',     'sort_order' => 5],
        ];

        foreach ($footerItems as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $footerMenu->id, 'url' => $item['url']],
                ['label' => $item['label'], 'sort_order' => $item['sort_order'], 'target' => '_self']
            );
        }
    }
}

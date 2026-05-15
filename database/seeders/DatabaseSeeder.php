<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\Footer;
use App\Models\Header;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Block;
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
        // ── 1. Roles ───────────────────────────────────────────────
        foreach (['superadmin', 'admin', 'editor'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // ── 2. Admin user ──────────────────────────────────────────
        $admin = User::updateOrCreate(
            ['email' => 'admin@hawkins.es'],
            ['name' => 'Administrador', 'password' => Hash::make('Hawkins2024!')]
        );
        $admin->syncRoles(['superadmin']);

        // ── 3. Headers ─────────────────────────────────────────────
        $headers = [
            [
                'name' => 'Classic (predeterminado)',
                'type' => 'classic',
                'layout' => 'logo_left',
                'bg_color' => '#ffffff',
                'text_color' => '#111111',
                'hover_color' => '#c9a96e',
                'active_color' => '#c9a96e',
                'logo_text' => config('app.name', 'Mi Empresa'),
                'logo_height' => 50,
                'sticky' => true,
                'transparent_on_top' => false,
                'cta_text' => 'Contactar',
                'cta_url' => '/contacto',
                'cta_bg_color' => '#111111',
                'cta_text_color' => '#ffffff',
                'show_search' => false,
                'show_social' => false,
                'is_default' => true,
            ],
            [
                'name' => 'Centered — Elegante',
                'type' => 'centered',
                'layout' => 'logo_left',
                'bg_color' => '#f7f5f2',
                'text_color' => '#333333',
                'hover_color' => '#c9a96e',
                'active_color' => '#c9a96e',
                'logo_text' => config('app.name', 'Mi Empresa'),
                'logo_height' => 60,
                'sticky' => false,
                'transparent_on_top' => false,
                'cta_text' => null,
                'cta_url' => null,
                'show_search' => false,
                'show_social' => true,
                'social_instagram' => 'https://instagram.com',
                'social_facebook' => 'https://facebook.com',
                'is_default' => false,
            ],
            [
                'name' => 'Split — Simétrico',
                'type' => 'split',
                'layout' => 'split',
                'bg_color' => '#111111',
                'text_color' => '#ffffff',
                'hover_color' => '#c9a96e',
                'active_color' => '#c9a96e',
                'logo_text' => config('app.name', 'Mi Empresa'),
                'logo_height' => 48,
                'sticky' => true,
                'transparent_on_top' => false,
                'cta_text' => 'Presupuesto',
                'cta_url' => '/contacto',
                'cta_bg_color' => '#c9a96e',
                'cta_text_color' => '#ffffff',
                'show_search' => false,
                'show_social' => false,
                'is_default' => false,
            ],
            [
                'name' => 'Minimal — Portfolio',
                'type' => 'minimal',
                'layout' => 'logo_left',
                'bg_color' => '#ffffff',
                'text_color' => '#111111',
                'hover_color' => '#c9a96e',
                'active_color' => '#c9a96e',
                'logo_text' => config('app.name', 'Mi Empresa'),
                'logo_height' => 44,
                'sticky' => true,
                'transparent_on_top' => true,
                'cta_text' => null,
                'cta_url' => null,
                'show_search' => false,
                'show_social' => true,
                'social_instagram' => 'https://instagram.com',
                'is_default' => false,
            ],
            [
                'name' => 'Mega — Empresa completa',
                'type' => 'mega',
                'layout' => 'logo_left',
                'bg_color' => '#ffffff',
                'text_color' => '#222222',
                'hover_color' => '#c9a96e',
                'active_color' => '#c9a96e',
                'logo_text' => config('app.name', 'Mi Empresa'),
                'logo_height' => 52,
                'sticky' => true,
                'transparent_on_top' => false,
                'phone' => '+34 956 000 000',
                'email' => 'info@miempresa.com',
                'cta_text' => 'Pedir cita',
                'cta_url' => '/contacto',
                'cta_bg_color' => '#c9a96e',
                'cta_text_color' => '#ffffff',
                'show_search' => false,
                'show_social' => true,
                'social_instagram' => 'https://instagram.com',
                'social_facebook' => 'https://facebook.com',
                'social_linkedin' => 'https://linkedin.com',
                'is_default' => false,
            ],
        ];

        $createdHeaders = [];
        foreach ($headers as $headerData) {
            $createdHeaders[$headerData['type']] = Header::updateOrCreate(
                ['name' => $headerData['name']],
                $headerData
            );
        }
        $defaultHeader = $createdHeaders['classic'];

        // ── 4. Footers ─────────────────────────────────────────────
        $footers = [
            [
                'name' => 'Classic (predeterminado)',
                'type' => 'classic',
                'bg_color' => '#111111',
                'text_color' => '#cccccc',
                'link_color' => '#c9a96e',
                'border_color' => '#2a2a2a',
                'logo_text' => config('app.name', 'Mi Empresa'),
                'tagline' => 'Creamos experiencias digitales que marcan la diferencia.',
                'phone' => '+34 956 000 000',
                'email' => 'info@miempresa.com',
                'address' => 'Algeciras, Cádiz, España',
                'copyright_text' => '© ' . date('Y') . ' ' . config('app.name', 'Mi Empresa') . '. Todos los derechos reservados.',
                'show_newsletter' => false,
                'social_instagram' => 'https://instagram.com',
                'social_facebook' => 'https://facebook.com',
                'social_linkedin' => 'https://linkedin.com',
                'is_default' => true,
            ],
            [
                'name' => 'Centered — Minimalista claro',
                'type' => 'centered',
                'bg_color' => '#f7f5f2',
                'text_color' => '#555555',
                'link_color' => '#c9a96e',
                'border_color' => '#e8e4df',
                'logo_text' => config('app.name', 'Mi Empresa'),
                'tagline' => 'Diseño con propósito.',
                'copyright_text' => '© ' . date('Y') . ' ' . config('app.name', 'Mi Empresa'),
                'show_newsletter' => false,
                'social_instagram' => 'https://instagram.com',
                'social_facebook' => 'https://facebook.com',
                'is_default' => false,
            ],
            [
                'name' => 'Dark — Premium con newsletter',
                'type' => 'dark',
                'bg_color' => '#0a0a0a',
                'text_color' => '#e0e0e0',
                'link_color' => '#c9a96e',
                'border_color' => '#1e1e1e',
                'logo_text' => config('app.name', 'Mi Empresa'),
                'tagline' => 'Innovación. Calidad. Resultados.',
                'phone' => '+34 956 000 000',
                'email' => 'info@miempresa.com',
                'address' => 'Algeciras, Cádiz, España',
                'copyright_text' => '© ' . date('Y') . ' ' . config('app.name', 'Mi Empresa') . '. Todos los derechos reservados.',
                'show_newsletter' => true,
                'newsletter_title' => 'Mantente informado',
                'newsletter_placeholder' => 'Tu email',
                'social_instagram' => 'https://instagram.com',
                'social_facebook' => 'https://facebook.com',
                'social_twitter' => 'https://twitter.com',
                'social_linkedin' => 'https://linkedin.com',
                'is_default' => false,
            ],
            [
                'name' => 'Minimal — Solo copyright',
                'type' => 'minimal',
                'bg_color' => '#f7f5f2',
                'text_color' => '#888888',
                'link_color' => '#c9a96e',
                'border_color' => '#e0dbd5',
                'logo_text' => null,
                'copyright_text' => '© ' . date('Y') . ' ' . config('app.name', 'Mi Empresa'),
                'show_newsletter' => false,
                'is_default' => false,
            ],
            [
                'name' => 'Mega — Corporativo completo',
                'type' => 'mega',
                'bg_color' => '#0f0f0f',
                'text_color' => '#dddddd',
                'link_color' => '#c9a96e',
                'border_color' => '#1f1f1f',
                'logo_text' => config('app.name', 'Mi Empresa'),
                'tagline' => 'Transformamos ideas en soluciones digitales extraordinarias.',
                'phone' => '+34 956 000 000',
                'email' => 'info@miempresa.com',
                'address' => 'Algeciras, Cádiz, España',
                'copyright_text' => '© ' . date('Y') . ' ' . config('app.name', 'Mi Empresa') . '. Todos los derechos reservados.',
                'show_newsletter' => true,
                'newsletter_title' => 'Únete a nuestra comunidad',
                'newsletter_placeholder' => 'Tu correo electrónico',
                'social_instagram' => 'https://instagram.com',
                'social_facebook' => 'https://facebook.com',
                'social_twitter' => 'https://twitter.com',
                'social_linkedin' => 'https://linkedin.com',
                'social_youtube' => 'https://youtube.com',
                'is_default' => false,
            ],
        ];

        $createdFooters = [];
        foreach ($footers as $footerData) {
            $createdFooters[$footerData['type']] = Footer::updateOrCreate(
                ['name' => $footerData['name']],
                $footerData
            );
        }
        $defaultFooter = $createdFooters['classic'];

        // ── 5. SiteSettings ────────────────────────────────────────
        SiteSettings::updateOrCreate(['id' => 1], [
            'site_name'          => config('app.name', 'Hawkins CMS'),
            'site_url'           => config('app.url'),
            'theme'              => 'sanzahra',
            'ecommerce_enabled'  => false,
            'payment_gateway'    => 'none',
            'accent_color'       => '#c9a96e',
            'font_heading'       => 'Cormorant Garamond',
            'font_body'          => 'Montserrat',
            'default_header_id'  => $defaultHeader->id,
            'default_footer_id'  => $defaultFooter->id,
            'maintenance_mode'   => false,
        ]);

        // ── 6. Páginas con bloques ─────────────────────────────────
        $pages = [
            // ─ INICIO ─
            [
                'page' => [
                    'title'        => 'Inicio',
                    'slug'         => 'home',
                    'status'       => 'published',
                    'published_at' => now(),
                    'meta_title'   => 'Inicio — ' . config('app.name', 'Mi Empresa'),
                    'meta_description' => 'Bienvenido a ' . config('app.name', 'Mi Empresa') . '. Soluciones digitales a medida para tu negocio.',
                ],
                'blocks' => [
                    [
                        'type' => 'hero',
                        'sort' => 1,
                        'content' => [
                            'title'            => 'Transformamos tu visión en realidad digital',
                            'subtitle'         => 'Diseño web, desarrollo y marketing digital para empresas que quieren destacar',
                            'background_image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1600&q=80',
                            'button_text'      => 'Conoce nuestros servicios',
                            'button_url'       => '/servicios',
                        ],
                    ],
                    [
                        'type' => 'counter',
                        'sort' => 2,
                        'content' => [
                            'bg_color' => '#111111',
                            'items' => [
                                ['number' => '200+', 'label' => 'Proyectos completados', 'icon' => '🚀'],
                                ['number' => '98%',  'label' => 'Clientes satisfechos',  'icon' => '⭐'],
                                ['number' => '10+',  'label' => 'Años de experiencia',   'icon' => '🏆'],
                                ['number' => '24/7', 'label' => 'Soporte disponible',    'icon' => '💬'],
                            ],
                        ],
                    ],
                    [
                        'type' => 'services',
                        'sort' => 3,
                        'content' => [
                            'title' => 'Nuestros servicios',
                            'items' => [
                                ['icon' => '🖥️', 'title' => 'Diseño Web',        'description' => 'Creamos sitios web modernos, rápidos y optimizados para convertir visitantes en clientes.'],
                                ['icon' => '📱', 'title' => 'Apps Móviles',      'description' => 'Desarrollamos aplicaciones nativas y multiplataforma con la mejor experiencia de usuario.'],
                                ['icon' => '🎯', 'title' => 'Marketing Digital', 'description' => 'Estrategias SEO, SEM y redes sociales para aumentar tu visibilidad y ventas online.'],
                                ['icon' => '🛒', 'title' => 'E-commerce',        'description' => 'Tiendas online profesionales con pasarelas de pago y gestión de inventario integrada.'],
                                ['icon' => '📊', 'title' => 'Analytics',         'description' => 'Análisis detallado de datos para tomar decisiones estratégicas basadas en métricas reales.'],
                                ['icon' => '🔒', 'title' => 'Seguridad Web',     'description' => 'Auditorías de seguridad, SSL, backups automáticos y protección contra amenazas.'],
                            ],
                        ],
                    ],
                    [
                        'type' => 'text_image',
                        'sort' => 4,
                        'content' => [
                            'title'          => '¿Por qué elegir ' . config('app.name', 'nosotros') . '?',
                            'body'           => "Llevamos más de una década ayudando a empresas como la tuya a crecer en el mundo digital.\n\nNuestro equipo de expertos combina creatividad, tecnología y estrategia para crear soluciones que realmente funcionan. No vendemos plantillas, creamos experiencias únicas adaptadas a cada cliente.\n\nDesde pequeñas empresas locales hasta grandes corporaciones, nuestro enfoque siempre es el mismo: resultados medibles y sostenibles.",
                            'image'          => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&q=80',
                            'image_position' => 'right',
                        ],
                    ],
                    [
                        'type' => 'testimonials',
                        'sort' => 5,
                        'content' => [
                            'title' => 'Lo que dicen nuestros clientes',
                            'items' => [
                                ['name' => 'María González', 'role' => 'CEO — Boutique Moda', 'text' => 'Increíble trabajo. Nuestra web nueva ha triplicado las ventas online en solo 3 meses. El equipo es profesional, rápido y siempre disponible.', 'rating' => 5],
                                ['name' => 'Carlos Ruiz', 'role' => 'Director — Clínica Dental', 'text' => 'La app que desarrollaron para nuestra clínica ha mejorado radicalmente la gestión de citas. Los pacientes están encantados y nosotros también.', 'rating' => 5],
                                ['name' => 'Ana Martínez', 'role' => 'Fundadora — Startup Tech', 'text' => 'Nos guiaron desde la idea hasta el lanzamiento. Su experiencia en UX/UI hizo que nuestro producto destacara en un mercado muy competido.', 'rating' => 5],
                            ],
                        ],
                    ],
                    [
                        'type' => 'cta',
                        'sort' => 6,
                        'content' => [
                            'title'       => '¿Listo para empezar tu proyecto?',
                            'subtitle'    => 'Cuéntanos tu idea y te preparamos una propuesta personalizada sin compromiso',
                            'button_text' => 'Solicitar presupuesto gratuito',
                            'button_url'  => '/contacto',
                        ],
                    ],
                ],
            ],

            // ─ SOBRE NOSOTROS ─
            [
                'page' => [
                    'title'        => 'Sobre Nosotros',
                    'slug'         => 'sobre-nosotros',
                    'status'       => 'published',
                    'published_at' => now(),
                    'meta_title'   => 'Sobre Nosotros — ' . config('app.name', 'Mi Empresa'),
                    'meta_description' => 'Conoce al equipo detrás de ' . config('app.name', 'Mi Empresa') . '. Pasión, experiencia y compromiso con cada proyecto.',
                ],
                'blocks' => [
                    [
                        'type' => 'hero',
                        'sort' => 1,
                        'content' => [
                            'title'            => 'Conoce a nuestro equipo',
                            'subtitle'         => 'Apasionados por la tecnología, comprometidos con tu éxito',
                            'background_image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1600&q=80',
                        ],
                    ],
                    [
                        'type' => 'text_image',
                        'sort' => 2,
                        'content' => [
                            'title'          => 'Nuestra historia',
                            'body'           => "Todo empezó en 2014 con una idea simple: crear tecnología que realmente ayude a las personas y empresas a crecer.\n\nDesde entonces hemos crecido de 2 a más de 20 profesionales, hemos completado más de 200 proyectos y hemos construido relaciones duraderas con clientes de toda España.\n\nNuestra cultura se basa en la transparencia, la excelencia técnica y el compromiso genuino con los objetivos de cada cliente. Para nosotros no eres un número más, eres un socio estratégico.",
                            'image'          => 'https://images.unsplash.com/photo-1531973576160-7125cd663d86?w=800&q=80',
                            'image_position' => 'left',
                        ],
                    ],
                    [
                        'type' => 'counter',
                        'sort' => 3,
                        'content' => [
                            'bg_color' => '#c9a96e',
                            'items'    => [
                                ['number' => '2014', 'label' => 'Año de fundación',      'icon' => '🏢'],
                                ['number' => '20+',  'label' => 'Profesionales',         'icon' => '👥'],
                                ['number' => '200+', 'label' => 'Proyectos entregados',  'icon' => '✅'],
                                ['number' => '15',   'label' => 'Premios y reconocimientos', 'icon' => '🏆'],
                            ],
                        ],
                    ],
                    [
                        'type' => 'team',
                        'sort' => 4,
                        'content' => [
                            'title'    => 'Nuestro equipo',
                            'subtitle' => 'Profesionales apasionados con años de experiencia',
                            'items'    => [
                                ['name' => 'Alejandro García', 'role' => 'CEO & Fundador',         'bio' => '15 años en el sector digital. Experto en estrategia y desarrollo de negocio.', 'photo' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&q=80'],
                                ['name' => 'Laura Sánchez',    'role' => 'Directora de Diseño',    'bio' => 'UX/UI designer con pasión por crear interfaces que enamoran a los usuarios.', 'photo' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=400&q=80'],
                                ['name' => 'Pablo Moreno',     'role' => 'Lead Developer',         'bio' => 'Arquitecto de software especializado en aplicaciones escalables y de alto rendimiento.', 'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80'],
                                ['name' => 'Sara Jiménez',     'role' => 'Marketing Manager',      'bio' => 'Especialista en SEO, SEM y estrategias de contenido que generan resultados reales.', 'photo' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&q=80'],
                            ],
                        ],
                    ],
                    [
                        'type' => 'timeline',
                        'sort' => 5,
                        'content' => [
                            'title' => 'Nuestra trayectoria',
                            'items' => [
                                ['year' => '2014', 'title' => 'Fundación',                   'description' => 'Comenzamos con 2 personas y una visión clara: hacer la tecnología accesible para todos los negocios.'],
                                ['year' => '2016', 'title' => 'Primer gran cliente',         'description' => 'Lanzamos el e-commerce de una cadena nacional de tiendas, superando los 1M€ en ventas el primer año.'],
                                ['year' => '2018', 'title' => 'Equipo de 10 personas',      'description' => 'El crecimiento nos permitió expandir el equipo e incorporar especialistas en UX, SEO y desarrollo móvil.'],
                                ['year' => '2020', 'title' => 'Digitalización acelerada',   'description' => 'Ayudamos a más de 50 empresas a reinventarse digitalmente durante la pandemia.'],
                                ['year' => '2023', 'title' => 'Premio Mejor Agencia Sur',   'description' => 'Reconocidos como la mejor agencia digital del sur de España por segundo año consecutivo.'],
                                ['year' => '2025', 'title' => 'Expansión internacional',    'description' => 'Abrimos operaciones en Portugal y Latinoamérica para seguir creciendo con nuestros clientes.'],
                            ],
                        ],
                    ],
                    [
                        'type' => 'logo_grid',
                        'sort' => 6,
                        'content' => [
                            'title'    => 'Empresas que confían en nosotros',
                            'subtitle' => 'Trabajamos con marcas líderes en sus sectores',
                            'logos'    => [
                                ['image' => 'https://picsum.photos/seed/logo1/200/80', 'alt' => 'Cliente 1'],
                                ['image' => 'https://picsum.photos/seed/logo2/200/80', 'alt' => 'Cliente 2'],
                                ['image' => 'https://picsum.photos/seed/logo3/200/80', 'alt' => 'Cliente 3'],
                                ['image' => 'https://picsum.photos/seed/logo4/200/80', 'alt' => 'Cliente 4'],
                                ['image' => 'https://picsum.photos/seed/logo5/200/80', 'alt' => 'Cliente 5'],
                                ['image' => 'https://picsum.photos/seed/logo6/200/80', 'alt' => 'Cliente 6'],
                            ],
                        ],
                    ],
                    [
                        'type' => 'cta',
                        'sort' => 7,
                        'content' => [
                            'title'       => 'Únete a nuestra familia de clientes',
                            'subtitle'    => 'Hablemos de cómo podemos ayudar a tu empresa',
                            'button_text' => 'Contactar ahora',
                            'button_url'  => '/contacto',
                        ],
                    ],
                ],
            ],

            // ─ SERVICIOS ─
            [
                'page' => [
                    'title'        => 'Servicios',
                    'slug'         => 'servicios',
                    'status'       => 'published',
                    'published_at' => now(),
                    'meta_title'   => 'Servicios — ' . config('app.name', 'Mi Empresa'),
                    'meta_description' => 'Diseño web, desarrollo de apps, marketing digital y más. Soluciones digitales completas para tu negocio.',
                ],
                'blocks' => [
                    [
                        'type' => 'hero',
                        'sort' => 1,
                        'content' => [
                            'title'            => 'Servicios que impulsan tu negocio',
                            'subtitle'         => 'Soluciones digitales completas, desde el diseño hasta el lanzamiento y más allá',
                            'background_image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=1600&q=80',
                        ],
                    ],
                    [
                        'type' => 'services',
                        'sort' => 2,
                        'content' => [
                            'title' => 'Lo que hacemos',
                            'items' => [
                                ['icon' => '🖥️', 'title' => 'Diseño y Desarrollo Web',  'description' => 'Sitios web modernos, rápidos y responsivos. WordPress, Laravel, React y más. Siempre adaptados a tu imagen de marca.'],
                                ['icon' => '📱', 'title' => 'Aplicaciones Móviles',     'description' => 'Apps nativas iOS/Android o multiplataforma con Flutter y React Native. UX impecable y rendimiento excepcional.'],
                                ['icon' => '🛒', 'title' => 'E-commerce',               'description' => 'Tiendas WooCommerce o Shopify con integración de pagos, gestión de stock y analítica avanzada.'],
                                ['icon' => '🎯', 'title' => 'SEO & Marketing Digital',  'description' => 'Posicionamiento orgánico, Google Ads, Meta Ads y estrategias de contenido que generan leads cualificados.'],
                                ['icon' => '🤖', 'title' => 'Inteligencia Artificial',  'description' => 'Chatbots, automatización de procesos y análisis predictivo para optimizar tu negocio.'],
                                ['icon' => '☁️', 'title' => 'Cloud & DevOps',           'description' => 'Infraestructura en la nube, CI/CD, monitorización y escalabilidad automática para tu aplicación.'],
                            ],
                        ],
                    ],
                    [
                        'type' => 'pricing',
                        'sort' => 3,
                        'content' => [
                            'title'    => 'Planes y precios',
                            'subtitle' => 'Elige el plan que mejor se adapte a tus necesidades',
                            'plans'    => [
                                [
                                    'name'        => 'Starter',
                                    'price'       => 'Desde 990€',
                                    'description' => 'Perfecto para pequeñas empresas',
                                    'features'    => "Diseño web hasta 5 páginas\nDiseño responsivo\nFormulario de contacto\nSEO básico\nHosting 1 año incluido\nSoporte 3 meses",
                                    'cta_text'    => 'Solicitar presupuesto',
                                    'cta_url'     => '/contacto',
                                    'highlighted' => false,
                                ],
                                [
                                    'name'        => 'Business',
                                    'price'       => 'Desde 2.490€',
                                    'description' => 'Para empresas en crecimiento',
                                    'features'    => "Diseño web hasta 15 páginas\nBlog integrado\nIntegración CRM\nSEO avanzado\nGoogle Analytics configurado\nHosting premium 1 año\nSoporte 12 meses",
                                    'cta_text'    => 'Solicitar presupuesto',
                                    'cta_url'     => '/contacto',
                                    'highlighted' => true,
                                ],
                                [
                                    'name'        => 'Enterprise',
                                    'price'       => 'A medida',
                                    'description' => 'Proyectos complejos y corporativos',
                                    'features'    => "Páginas ilimitadas\nE-commerce completo\nIntegraciones custom\nApp móvil incluida\nSEO & SEM completo\nSoporte prioritario 24/7\nDesarrollo continuo",
                                    'cta_text'    => 'Hablar con un experto',
                                    'cta_url'     => '/contacto',
                                    'highlighted' => false,
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'faq',
                        'sort' => 4,
                        'content' => [
                            'title'    => 'Preguntas frecuentes',
                            'subtitle' => 'Todo lo que necesitas saber antes de empezar',
                            'items'    => [
                                ['question' => '¿Cuánto tiempo tarda en estar lista mi web?', 'answer' => 'El tiempo depende del proyecto. Una web informativa estándar tarda entre 3 y 6 semanas. Un e-commerce complejo puede llevar 2-3 meses. Siempre te damos un calendario detallado al inicio del proyecto.'],
                                ['question' => '¿Puedo actualizar el contenido yo mismo?', 'answer' => 'Sí, absolutamente. Todas nuestras webs incluyen un panel de administración intuitivo (WordPress o CMS propio) que te permite editar textos, imágenes y añadir contenido sin conocimientos técnicos.'],
                                ['question' => '¿Qué incluye el mantenimiento?', 'answer' => 'El mantenimiento incluye actualizaciones de seguridad, backups diarios, monitorización del rendimiento, corrección de errores y soporte técnico por email y teléfono según el plan contratado.'],
                                ['question' => '¿Trabajáis con empresas de fuera de Andalucía?', 'answer' => 'Sí, trabajamos con clientes de toda España y también internacionalmente. Toda nuestra gestión de proyectos es 100% online, con reuniones por videoconferencia cuando es necesario.'],
                                ['question' => '¿Qué pasa si no estoy satisfecho con el resultado?', 'answer' => 'Ofrecemos rondas ilimitadas de revisiones durante el proyecto hasta que el resultado sea exactamente lo que necesitas. Tu satisfacción es nuestra prioridad.'],
                            ],
                        ],
                    ],
                    [
                        'type' => 'cta',
                        'sort' => 5,
                        'content' => [
                            'title'       => 'Hablemos de tu proyecto',
                            'subtitle'    => 'Cuéntanos lo que necesitas y te preparamos una propuesta gratuita',
                            'button_text' => 'Solicitar presupuesto',
                            'button_url'  => '/contacto',
                        ],
                    ],
                ],
            ],

            // ─ CONTACTO ─
            [
                'page' => [
                    'title'        => 'Contacto',
                    'slug'         => 'contacto',
                    'status'       => 'published',
                    'published_at' => now(),
                    'meta_title'   => 'Contacto — ' . config('app.name', 'Mi Empresa'),
                    'meta_description' => 'Contacta con nosotros. Estamos aquí para ayudarte a hacer crecer tu negocio digital.',
                    'header_variant' => 'minimal',
                ],
                'blocks' => [
                    [
                        'type' => 'hero',
                        'sort' => 1,
                        'content' => [
                            'title'            => 'Hablemos',
                            'subtitle'         => 'Cuéntanos tu proyecto y te respondemos en menos de 24 horas',
                            'background_image' => 'https://images.unsplash.com/photo-1423666639041-f56000c27a9a?w=1600&q=80',
                        ],
                    ],
                    [
                        'type' => 'contact_form',
                        'sort' => 2,
                        'content' => [
                            'title' => '¿En qué podemos ayudarte?',
                        ],
                    ],
                    [
                        'type' => 'counter',
                        'sort' => 3,
                        'content' => [
                            'bg_color' => '#f7f5f2',
                            'items'    => [
                                ['number' => '< 24h', 'label' => 'Tiempo de respuesta',  'icon' => '⚡'],
                                ['number' => 'Gratis', 'label' => 'Primera consulta',    'icon' => '🎁'],
                                ['number' => '100%',   'label' => 'Satisfacción garantizada', 'icon' => '✅'],
                            ],
                        ],
                    ],
                    [
                        'type' => 'map',
                        'sort' => 4,
                        'content' => [
                            'address'   => 'Algeciras, Cádiz, España',
                            'embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12809.77738580899!2d-5.4661!3d36.1284!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd0cb8b8c3e2c531%3A0x40fb60fce2d3310!2sAlgeciras%2C%20C%C3%A1diz!5e0!3m2!1ses!2ses!4v1620000000000',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($pages as $pageData) {
            $page = Page::updateOrCreate(
                ['slug' => $pageData['page']['slug']],
                $pageData['page']
            );

            // Borra bloques previos y recrea
            $page->blocks()->delete();
            foreach ($pageData['blocks'] as $blockData) {
                Block::create([
                    'page_id' => $page->id,
                    'type'    => $blockData['type'],
                    'sort'    => $blockData['sort'],
                    'content' => $blockData['content'],
                ]);
            }
        }

        // ── 7. Menús ───────────────────────────────────────────────
        $headerMenu = Menu::updateOrCreate(['location' => 'header'], ['name' => 'Menú principal', 'location' => 'header']);
        $headerMenu->items()->delete();
        $navItems = [
            ['label' => 'Inicio',          'url' => '/',              'sort' => 1],
            ['label' => 'Sobre Nosotros',  'url' => '/sobre-nosotros','sort' => 2],
            ['label' => 'Servicios',       'url' => '/servicios',     'sort' => 3],
            ['label' => 'Blog',            'url' => '/blog',          'sort' => 4],
            ['label' => 'Contacto',        'url' => '/contacto',      'sort' => 5],
        ];
        foreach ($navItems as $item) {
            MenuItem::create(array_merge($item, ['menu_id' => $headerMenu->id]));
        }

        $footerMenu = Menu::updateOrCreate(['location' => 'footer'], ['name' => 'Menú footer', 'location' => 'footer']);
        $footerMenu->items()->delete();
        $footerItems = [
            ['label' => 'Inicio',         'url' => '/',               'sort' => 1],
            ['label' => 'Sobre Nosotros', 'url' => '/sobre-nosotros', 'sort' => 2],
            ['label' => 'Servicios',      'url' => '/servicios',      'sort' => 3],
            ['label' => 'Blog',           'url' => '/blog',           'sort' => 4],
            ['label' => 'Contacto',       'url' => '/contacto',       'sort' => 5],
            ['label' => 'Aviso legal',    'url' => '/aviso-legal',    'sort' => 6],
            ['label' => 'Privacidad',     'url' => '/privacidad',     'sort' => 7],
        ];
        foreach ($footerItems as $item) {
            MenuItem::create(array_merge($item, ['menu_id' => $footerMenu->id]));
        }

        // ── 8. Blog ────────────────────────────────────────────────
        $cat = Category::updateOrCreate(
            ['slug' => 'sin-categoria'],
            ['name' => 'Sin categoría', 'slug' => 'sin-categoria']
        );

        Post::updateOrCreate(
            ['slug' => 'bienvenido-a-hawkins-cms'],
            [
                'category_id'      => $cat->id,
                'title'            => 'Bienvenido a Hawkins CMS',
                'slug'             => 'bienvenido-a-hawkins-cms',
                'excerpt'          => 'Te presentamos nuestra plataforma de gestión de contenidos moderna, flexible y fácil de usar.',
                'body'             => '<p>¡Bienvenido! Este es tu primer post en el CMS. Desde el panel de administración puedes crear, editar y publicar contenido de forma sencilla.</p><p>Explora todas las funcionalidades disponibles y empieza a construir tu presencia digital.</p>',
                'status'           => 'published',
                'featured'         => true,
                'published_at'     => now(),
                'featured_image'   => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=800&q=80',
                'meta_title'       => 'Bienvenido a Hawkins CMS',
                'meta_description' => 'Primera publicación del blog. Descubre todo lo que puedes hacer con Hawkins CMS.',
            ]
        );
    }
}

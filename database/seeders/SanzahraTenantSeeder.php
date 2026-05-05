<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\Header;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class SanzahraTenantSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tenant (withoutEvents evita que stancl cree BD separada en single-DB mode)
        $tenant = Tenant::withoutEvents(fn () => Tenant::firstOrCreate(
            ['id' => 'sanzahra'],
            [
                'name'               => 'Sanzahra',
                'theme'              => 'sanzahra',
                'ecommerce_enabled'  => false,
                'header_layout'      => 'center',
                'payment_gateway'    => 'none',
            ]
        ));

        // Ensure name/theme are set on existing record too
        $tenant->update([
            'name'          => 'Sanzahra',
            'theme'         => 'sanzahra',
            'header_layout' => 'center',
        ]);

        // 2. Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@sanzahra.com'],
            [
                'name'               => 'Admin Sanzahra',
                'password'           => bcrypt('Sanzahra2024!'),
                'tenant_id'          => $tenant->id,
                'email_verified_at'  => now(),
            ]
        );

        if ($admin->wasRecentlyCreated) {
            try {
                $admin->assignRole('admin');
            } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
                // Role not yet seeded — skip silently
            }
        }

        // 3. Header
        Header::updateOrCreate(
            ['tenant_id' => $tenant->id],
            ['layout' => 'split', 'bg_color' => '#ffffff', 'text_color' => '#1a1a1a']
        );

        // 4. Menu items
        MenuItem::where('tenant_id', $tenant->id)->delete();

        $menuNav = [
            ['label' => 'Inicio',                'url' => '/'],
            ['label' => 'Sobre Nosotros',         'url' => '/sobre-nosotros'],
            ['label' => 'Servicios',              'url' => '/servicios'],
            ['label' => 'Moda',                   'url' => '/moda'],
            ['label' => 'Branding',               'url' => '/branding'],
            ['label' => 'Producción de Eventos',  'url' => '/event-producer'],
            ['label' => 'Interiorismo',           'url' => '/interiorismo'],
            ['label' => 'Asistencia Ejecutiva',   'url' => '/asistencia-ejecutiva'],
            ['label' => 'Portfolio',              'url' => '/portfolio'],
            ['label' => 'Contacto',              'url' => '/contacto'],
        ];

        foreach ($menuNav as $sort => $item) {
            MenuItem::create([
                'tenant_id' => $tenant->id,
                'label'     => $item['label'],
                'url'       => $item['url'],
                'sort'      => $sort + 1,
                'parent_id' => null,
            ]);
        }

        // 5. Pages — skip already-existing ones
        $this->seedPages($tenant->id);
    }

    private function seedPages(string $tenantId): void
    {
        $pages = $this->pagesData();

        foreach ($pages as $pageData) {
            $existing = Page::withoutGlobalScopes()->where('tenant_id', $tenantId)->where('slug', $pageData['slug'])->first();
            if ($existing) {
                continue;
            }

            $page = Page::create([
                'tenant_id'    => $tenantId,
                'title'        => $pageData['title'],
                'slug'         => $pageData['slug'],
                'status'       => 'published',
                'published_at' => now(),
            ]);

            foreach ($pageData['blocks'] as $sort => $blockData) {
                Block::create([
                    'page_id'   => $page->id,
                    'tenant_id' => $tenantId,
                    'type'      => $blockData['type'],
                    'content'   => $blockData['content'],
                    'sort'      => $sort + 1,
                ]);
            }
        }
    }

    private function pagesData(): array
    {
        return [
            // ── Inicio ──────────────────────────────────────────────────────
            [
                'title'  => 'Inicio',
                'slug'   => 'home',
                'blocks' => [
                    [
                        'type'    => 'hero',
                        'content' => [
                            'title'            => 'SANZAHRA',
                            'subtitle'         => 'Moda · Branding · Producción de Eventos · Interiorismo',
                            'background_image' => 'assets/img/moda/moda-06.jpg',
                        ],
                    ],
                    [
                        'type'    => 'services',
                        'content' => [
                            'title' => 'Nuestros Servicios',
                            'items' => [
                                ['title' => 'Moda',                 'description' => 'Marcas propias, colaboraciones, talento e identidad de marcas de moda.',                                     'icon' => '✦'],
                                ['title' => 'Branding & Marketing', 'description' => 'Identidad, narrativa y posicionamiento que transforman marcas en símbolos.',                                  'icon' => '✦'],
                                ['title' => 'Producción de Eventos','description' => 'Galas, pasarelas, lanzamientos y activaciones que se viven antes de contarse.',                               'icon' => '✦'],
                                ['title' => 'Interiorismo',         'description' => 'Espacios íntimos y hoteleros que se habitan como si fueran propios.',                                         'icon' => '✦'],
                                ['title' => 'Asistencia Ejecutiva', 'description' => 'Concierge silencioso y exigente para quien no tiene tiempo que perder.',                                      'icon' => '✦'],
                                ['title' => 'Arquitectura',         'description' => 'Arquitectura conceptual, neuronal y efímera con vocación de permanecer.',                                     'icon' => '✦'],
                            ],
                        ],
                    ],
                    [
                        'type'    => 'cta',
                        'content' => [
                            'title'       => '¿Tienes un proyecto en mente?',
                            'subtitle'    => 'Estamos listos para escucharte. Cada gran proyecto empieza con una conversación.',
                            'button_text' => 'Contáctanos',
                            'button_url'  => '/contacto',
                        ],
                    ],
                ],
            ],

            // ── Sobre Nosotros ───────────────────────────────────────────────
            [
                'title'  => 'Sobre Nosotros',
                'slug'   => 'sobre-nosotros',
                'blocks' => [
                    [
                        'type'    => 'hero',
                        'content' => [
                            'title'            => 'SANZAHRA',
                            'subtitle'         => 'Una visión, múltiples disciplinas. Creamos donde la estética se encuentra con la estrategia.',
                            'background_image' => 'assets/img/extra/extra-02.jpg',
                        ],
                    ],
                    [
                        'type'    => 'text_image',
                        'content' => [
                            'title'          => 'La precisión es nuestra forma de arte',
                            'body'           => 'Creemos que cada proyecto merece la misma dedicación sin importar su escala. Desde la concepción de una marca hasta la producción de una pasarela, aplicamos el mismo rigor estético y estratégico. Nuestra multidisciplinariedad no es casualidad: es el resultado de entender que el lujo contemporáneo exige visiones integrales.',
                            'image'          => 'assets/img/moda/moda-34-ilustraciones-rosa.jpg',
                            'image_position' => 'right',
                        ],
                    ],
                ],
            ],

            // ── Servicios ────────────────────────────────────────────────────
            [
                'title'  => 'Servicios',
                'slug'   => 'servicios',
                'blocks' => [
                    [
                        'type'    => 'hero',
                        'content' => [
                            'title'            => 'Soluciones creativas a medida',
                            'subtitle'         => 'Seis disciplinas integradas bajo una misma visión. Trabajamos solos o en conjunto, según el proyecto lo requiera.',
                            'background_image' => 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?w=1920&q=80',
                        ],
                    ],
                    [
                        'type'    => 'services',
                        'content' => [
                            'title' => 'Todo lo que tu marca necesita, en un solo lugar',
                            'items' => [
                                ['title' => 'Moda',                  'description' => 'Creación de marcas propias, colaboraciones con diseñadores, captación de talento y construcción de identidad para firmas de moda.',                                       'icon' => '01'],
                                ['title' => 'Branding & Marketing',  'description' => 'Construimos marcas con alma: desde la estrategia y el naming hasta el sistema visual completo, el tono de voz y la narrativa que las sostiene en el tiempo.',             'icon' => '02'],
                                ['title' => 'Producción de Eventos', 'description' => 'Producción integral de pasarelas, desfiles y eventos de moda. Del casting a la última luz: dirección creativa, escenografía, técnica y show.',                             'icon' => '03'],
                                ['title' => 'Interiorismo',          'description' => 'Espacios que cuentan historias. Trabajamos residencias privadas de alta gama, hotelería 5 estrellas y locales comerciales.',                                                'icon' => '04'],
                                ['title' => 'Asistencia Ejecutiva',  'description' => 'Concierge ejecutivo de alto nivel para directivos, familias y patrimonio privado. Discreción absoluta, disponibilidad total.',                                               'icon' => '05'],
                                ['title' => 'Arquitectura',          'description' => 'Proyectos arquitectónicos donde forma y función se equilibran con precisión. Desarrollados en colaboración con Sumnomatec.',                                                  'icon' => '06'],
                            ],
                        ],
                    ],
                ],
            ],

            // ── Moda ────────────────────────────────────────────────────────
            [
                'title'  => 'Moda',
                'slug'   => 'moda',
                'blocks' => [
                    [
                        'type'    => 'hero',
                        'content' => [
                            'title'            => 'La moda como forma de cultura',
                            'subtitle'         => 'Creación de marcas, producción de desfiles y gestión de talento. Vestir ideas antes que cuerpos.',
                            'background_image' => 'assets/img/moda/moda-24-editorial-bw.jpg',
                        ],
                    ],
                    [
                        'type'    => 'text_image',
                        'content' => [
                            'title'          => 'Vestir ideas, no tendencias',
                            'body'           => 'La moda es un lenguaje: dice quiénes somos antes incluso de que hablemos. En SANZAHRA abordamos la moda como una forma de cultura contemporánea, más cerca del arte y la antropología que del mero consumo. Creamos marcas propias, producimos eventos de moda como experiencias narrativas y acompañamos a la nueva generación de diseñadores.',
                            'image'          => 'assets/img/moda/moda-23-ilustraciones.jpg',
                            'image_position' => 'right',
                        ],
                    ],
                    [
                        'type'    => 'gallery',
                        'content' => [
                            'columns' => 3,
                            'images'  => [
                                'assets/img/moda/moda-06.jpg',
                                'assets/img/moda/moda-16.jpg',
                                'assets/img/moda/moda-08.jpg',
                                'assets/img/moda/moda-25-bocetos.jpg',
                                'assets/img/moda/moda-36-joyeria-glitch2.jpg',
                                'assets/img/moda/moda-37-anillos-guantes2.jpg',
                            ],
                        ],
                    ],
                ],
            ],

            // ── Branding ─────────────────────────────────────────────────────
            [
                'title'  => 'Branding',
                'slug'   => 'branding',
                'blocks' => [
                    [
                        'type'    => 'hero',
                        'content' => [
                            'title'            => 'Branding & Marketing',
                            'subtitle'         => 'Identidad, narrativa y posicionamiento que transforman marcas en símbolos.',
                            'background_image' => 'assets/img/branding/branding-05.jpg',
                        ],
                    ],
                    [
                        'type'    => 'text_image',
                        'content' => [
                            'title'          => 'Construimos marcas con alma',
                            'body'           => 'Desde la estrategia y el naming hasta el sistema visual completo, el tono de voz y la narrativa que las sostiene en el tiempo. Estrategia de marca y posicionamiento, naming, identidad visual y manual de marca, dirección de arte y campañas.',
                            'image'          => 'assets/img/branding/branding-02.jpg',
                            'image_position' => 'left',
                        ],
                    ],
                    [
                        'type'    => 'gallery',
                        'content' => [
                            'columns' => 3,
                            'images'  => [
                                'assets/img/branding/branding-02.jpg',
                                'assets/img/branding/branding-04.jpg',
                                'assets/img/branding/branding-05.jpg',
                                'assets/img/branding/branding-07.jpg',
                            ],
                        ],
                    ],
                ],
            ],

            // ── Producción de Eventos ────────────────────────────────────────
            [
                'title'  => 'Producción de Eventos',
                'slug'   => 'event-producer',
                'blocks' => [
                    [
                        'type'    => 'hero',
                        'content' => [
                            'title'            => 'Producción de Eventos',
                            'subtitle'         => 'Galas, pasarelas, lanzamientos y activaciones que se viven antes de contarse.',
                            'background_image' => 'assets/img/extra/extra-eventos-flores.jpg',
                        ],
                    ],
                    [
                        'type'    => 'text_image',
                        'content' => [
                            'title'          => 'Del casting a la última luz',
                            'body'           => 'Producción integral de pasarelas, desfiles y eventos de moda. Dirección creativa, escenografía, técnica y show. Pasarelas y desfiles de moda, presentaciones de colección, lanzamientos de marca, editoriales en vivo.',
                            'image'          => 'assets/img/eventos/eventos-13-pasarela-flores.jpg',
                            'image_position' => 'right',
                        ],
                    ],
                ],
            ],

            // ── Interiorismo ─────────────────────────────────────────────────
            [
                'title'  => 'Interiorismo',
                'slug'   => 'interiorismo',
                'blocks' => [
                    [
                        'type'    => 'hero',
                        'content' => [
                            'title'            => 'Interiorismo',
                            'subtitle'         => 'Espacios íntimos y hoteleros que se habitan como si fueran propios.',
                            'background_image' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1920&q=80',
                        ],
                    ],
                    [
                        'type'    => 'text_image',
                        'content' => [
                            'title'          => 'Espacios que cuentan historias',
                            'body'           => 'Trabajamos residencias privadas de alta gama, hotelería 5 estrellas y locales comerciales donde cada material y cada luz está elegido con intención. Residencial de lujo, hotelería y restauración, retail y showrooms, espacios efímeros y pop-up.',
                            'image'          => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=900&q=80',
                            'image_position' => 'right',
                        ],
                    ],
                ],
            ],

            // ── Asistencia Ejecutiva ─────────────────────────────────────────
            [
                'title'  => 'Asistencia Ejecutiva',
                'slug'   => 'asistencia-ejecutiva',
                'blocks' => [
                    [
                        'type'    => 'hero',
                        'content' => [
                            'title'            => 'Asistencia Ejecutiva',
                            'subtitle'         => 'Concierge silencioso y exigente para quien no tiene tiempo que perder.',
                            'background_image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920&q=80',
                        ],
                    ],
                    [
                        'type'    => 'text_image',
                        'content' => [
                            'title'          => 'Discreción absoluta, disponibilidad total',
                            'body'           => 'Concierge ejecutivo de alto nivel para directivos, familias y patrimonio privado. Gestión de agenda y viajes, coordinación de equipos personales, gestión de patrimonio y propiedades, lifestyle management.',
                            'image'          => 'assets/img/branding/branding-04.jpg',
                            'image_position' => 'right',
                        ],
                    ],
                ],
            ],

            // ── Arquitectura ─────────────────────────────────────────────────
            [
                'title'  => 'Arquitectura',
                'slug'   => 'arquitectura',
                'blocks' => [
                    [
                        'type'    => 'hero',
                        'content' => [
                            'title'            => 'Arquitectura',
                            'subtitle'         => 'Arquitectura conceptual, neuronal y efímera con vocación de permanecer.',
                            'background_image' => 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?w=1920&q=80',
                        ],
                    ],
                    [
                        'type'    => 'text_image',
                        'content' => [
                            'title'          => 'Forma y función en equilibrio',
                            'body'           => 'Proyectos arquitectónicos desarrollados en colaboración con Sumnomatec. Sedes corporativas y oficinas, viviendas unifamiliares, pabellones y arquitectura efímera, rehabilitación y patrimonio.',
                            'image'          => 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?w=900&q=80',
                            'image_position' => 'right',
                        ],
                    ],
                ],
            ],

            // ── Portfolio ────────────────────────────────────────────────────
            [
                'title'  => 'Portfolio',
                'slug'   => 'portfolio',
                'blocks' => [
                    [
                        'type'    => 'hero',
                        'content' => [
                            'title'            => 'Portfolio',
                            'subtitle'         => 'Proyectos seleccionados — Moda, Eventos, Branding e Interiorismo.',
                            'background_image' => 'assets/img/extra/extra-01.jpg',
                        ],
                    ],
                    [
                        'type'    => 'gallery',
                        'content' => [
                            'columns' => 3,
                            'images'  => [
                                'assets/img/extra/extra-01.jpg',
                                'assets/img/moda/moda-16.jpg',
                                'assets/img/moda/moda-08.jpg',
                                'assets/img/branding/branding-07.jpg',
                                'assets/img/extra/extra-04.jpg',
                                'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=600&q=80',
                            ],
                        ],
                    ],
                ],
            ],

            // ── Contacto ─────────────────────────────────────────────────────
            [
                'title'  => 'Contacto',
                'slug'   => 'contacto',
                'blocks' => [
                    [
                        'type'    => 'contact_form',
                        'content' => [
                            'title'   => 'Contáctanos',
                            'email'   => 'info@sanzahra.com',
                            'address' => 'Calle Córdoba 6, Málaga',
                            'phone'   => '+34 646 63 95 58',
                        ],
                    ],
                ],
            ],

            // ── Política de Privacidad ───────────────────────────────────────
            [
                'title'  => 'Política de Privacidad',
                'slug'   => 'politica-privacidad',
                'blocks' => [
                    [
                        'type'    => 'text_image',
                        'content' => [
                            'title'          => 'Política de Privacidad',
                            'body'           => 'SANZAHRA trata tus datos personales con la máxima confidencialidad y de acuerdo con el Reglamento General de Protección de Datos (RGPD) y la Ley Orgánica de Protección de Datos española. Los datos recabados a través de nuestros formularios se utilizan exclusivamente para gestionar tu consulta o solicitud. No cedemos datos a terceros sin tu consentimiento expreso. Puedes ejercer tus derechos de acceso, rectificación, supresión y portabilidad escribiendo a info@sanzahra.com.',
                            'image'          => '',
                            'image_position' => 'right',
                        ],
                    ],
                ],
            ],

            // ── Términos y Condiciones ────────────────────────────────────────
            [
                'title'  => 'Términos y Condiciones',
                'slug'   => 'terminos-condiciones',
                'blocks' => [
                    [
                        'type'    => 'text_image',
                        'content' => [
                            'title'          => 'Términos y Condiciones',
                            'body'           => 'El acceso y uso de este sitio web implica la aceptación de los presentes términos y condiciones. SANZAHRA se reserva el derecho a modificar los contenidos sin previo aviso. Toda la propiedad intelectual (textos, imágenes, diseño) pertenece a Sanzahra Atelier S.L. y está protegida por la legislación vigente. Queda prohibida su reproducción total o parcial sin autorización expresa. Para cualquier consulta legal: info@sanzahra.com.',
                            'image'          => '',
                            'image_position' => 'right',
                        ],
                    ],
                ],
            ],
        ];
    }
}

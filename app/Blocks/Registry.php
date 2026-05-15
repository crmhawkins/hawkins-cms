<?php

namespace App\Blocks;

class Registry
{
    public static function all(): array
    {
        return [
            'hero', 'gallery', 'text_image', 'contact_form', 'services', 'cta', 'map', 'shop',
            'testimonials', 'faq', 'team', 'video', 'counter', 'accordion', 'pricing', 'timeline', 'logo_grid', 'banner',
            'text', 'image', 'spacer', 'divider', 'columns',
        ];
    }

    public static function schema(string $type): array
    {
        return match ($type) {
            'hero'         => ['title' => '', 'subtitle' => '', 'image' => '', 'button_text' => '', 'button_url' => ''],
            'gallery'      => ['images' => [], 'columns' => 3],
            'text_image'   => ['title' => '', 'body' => '', 'image' => '', 'image_position' => 'right'],
            'contact_form' => ['title' => 'Contacto', 'email' => ''],
            'services'     => ['title' => '', 'items' => []],
            'cta'          => ['title' => '', 'subtitle' => '', 'button_text' => '', 'button_url' => ''],
            'map'          => ['address' => '', 'embed_url' => '', 'zoom' => 15],
            'shop'         => ['title' => 'Nuestra Tienda', 'show_featured' => true, 'max_products' => 6],
            'testimonials' => ['title' => '', 'items' => []],
            'faq'          => ['title' => '', 'subtitle' => '', 'items' => []],
            'team'         => ['title' => '', 'subtitle' => '', 'items' => []],
            'video'        => ['title' => '', 'subtitle' => '', 'video_url' => '', 'cover_image' => '', 'autoplay' => false],
            'counter'      => ['title' => '', 'bg_color' => '', 'items' => []],
            'accordion'    => ['title' => '', 'items' => []],
            'pricing'      => ['title' => '', 'subtitle' => '', 'plans' => []],
            'timeline'     => ['title' => '', 'items' => []],
            'logo_grid'    => ['title' => '', 'subtitle' => '', 'logos' => []],
            'banner'       => ['text' => '', 'cta_text' => '', 'cta_url' => '', 'bg_color' => '', 'text_color' => '', 'dismissible' => false],
            'text'         => ['title' => '', 'body' => '', 'text_align' => 'left', 'max_width' => 'normal', 'font_size' => '1', 'line_height' => '1.75', 'text_color' => ''],
            'image'        => ['src' => '', 'alt' => '', 'caption' => '', 'align' => 'center', 'max_width' => '100%', 'border_radius' => 0, 'shadow' => false, 'link_url' => '', 'link_new_tab' => false],
            'spacer'       => ['height' => 60],
            'divider'      => ['style' => 'solid', 'color' => '#e0dbd5', 'thickness' => 1, 'width' => '100%', 'padding' => 20],
            'columns'      => ['vertical_align' => 'top', 'gap' => '2rem', 'columns' => []],
            default        => [],
        };
    }

    public static function label(string $type): string
    {
        return match ($type) {
            'hero'         => 'Hero / Cabecera',
            'gallery'      => 'Galería',
            'text_image'   => 'Texto + Imagen',
            'contact_form' => 'Formulario de Contacto',
            'services'     => 'Servicios',
            'cta'          => 'Llamada a la Acción',
            'map'          => 'Mapa',
            'shop'         => 'Tienda Online',
            'testimonials' => 'Testimonios',
            'faq'          => 'Preguntas frecuentes',
            'team'         => 'Equipo',
            'video'        => 'Vídeo',
            'counter'      => 'Estadísticas / Contadores',
            'accordion'    => 'Acordeón',
            'pricing'      => 'Tabla de precios',
            'timeline'     => 'Línea temporal',
            'logo_grid'    => 'Grid de logos',
            'banner'       => 'Banner anuncio',
            'text'         => '📝 Texto enriquecido',
            'image'        => '🖼️ Imagen',
            'spacer'       => '↕️ Espaciador',
            'divider'      => '➖ Divisor / Separador',
            'columns'      => '▦ Columnas',
            default        => $type,
        };
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::all() as $type) {
            $options[$type] = self::label($type);
        }

        return $options;
    }
}

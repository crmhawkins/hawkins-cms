<?php

namespace App\Blocks;

class Registry
{
    public static function all(): array
    {
        return ['hero', 'gallery', 'text_image', 'contact_form', 'services', 'cta', 'map', 'shop'];
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

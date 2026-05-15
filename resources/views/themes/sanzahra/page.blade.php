@extends('themes.sanzahra.layouts.app')

@section('content')
    @foreach($page->blocks as $block)
        @php
            $blockId  = 'block-' . $block->id;
            $style    = '';
            $wrapStyle = '';

            // Fondo y texto
            if ($block->bg_color)   $style .= "background:{$block->bg_color};";
            if ($block->text_color) $style .= "color:{$block->text_color};";

            // Padding (en px)
            $pt = $block->padding_top    ? "padding-top:{$block->padding_top}px;"    : '';
            $pb = $block->padding_bottom ? "padding-bottom:{$block->padding_bottom}px;" : '';
            $px = $block->padding_x      ? "padding-left:{$block->padding_x}px;padding-right:{$block->padding_x}px;" : '';
            $style .= $pt . $pb . $px;

            // Márgenes
            if ($block->margin_top)    $wrapStyle .= "margin-top:{$block->margin_top}px;";
            if ($block->margin_bottom) $wrapStyle .= "margin-bottom:{$block->margin_bottom}px;";

            // Ancho container
            $maxWidths = [
                'full'   => '100%',
                'wide'   => '1400px',
                'normal' => '1200px',
                'narrow' => '800px',
            ];
            $containerMax = $maxWidths[$block->container_width ?? 'normal'] ?? '1200px';

            // CSS class
            $extraClass = $block->css_class ?? '';
        @endphp

        {{-- Wrapper con separador superior --}}
        <div id="{{ $blockId }}" class="cms-block cms-block-{{ $block->type }} {{ $extraClass }}" style="{{ $wrapStyle }}">

            {{-- Separador TOP --}}
            @if($block->separator_top && $block->separator_top !== 'none')
                @include('themes.sanzahra.partials.separator', [
                    'type'     => $block->separator_top,
                    'color'    => $block->separator_color ?? ($block->bg_color ?? '#ffffff'),
                    'position' => 'top',
                ])
            @endif

            {{-- Bloque con estilo aplicado --}}
            <div class="cms-block-inner" style="{{ $style }}">
                @if($block->custom_css)
                    <style>#{{ $blockId }} { {{ $block->custom_css }} }</style>
                @endif

                @if($block->full_width)
                    @includeIf("themes.sanzahra.blocks.{$block->type}", ['block' => $block])
                @else
                    <div class="cms-container" style="max-width:{{ $containerMax }};margin:0 auto;">
                        @includeIf("themes.sanzahra.blocks.{$block->type}", ['block' => $block])
                    </div>
                @endif
            </div>

            {{-- Separador BOTTOM --}}
            @if($block->separator_bottom && $block->separator_bottom !== 'none')
                @include('themes.sanzahra.partials.separator', [
                    'type'     => $block->separator_bottom,
                    'color'    => $block->separator_color ?? '#ffffff',
                    'position' => 'bottom',
                ])
            @endif
        </div>
    @endforeach
@endsection

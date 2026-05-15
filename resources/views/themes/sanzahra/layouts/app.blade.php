<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $resolvedHeader = isset($page) && method_exists($page, 'resolvedHeader') ? $page->resolvedHeader() : \App\Models\Header::getDefault();
        $resolvedFooter = isset($page) && method_exists($page, 'resolvedFooter') ? $page->resolvedFooter() : \App\Models\Footer::getDefault();
        $headerType = $resolvedHeader->type ?? 'classic';
        $footerType = $resolvedFooter->type ?? 'classic';
        $settings   = \App\Models\SiteSettings::instance();

        $siteName  = config('app.name');
        $seoTitle  = isset($page) ? ($page->seoTitle() . ' — ' . $siteName) : ($title ?? $siteName);
        $seoDesc   = isset($page) ? $page->seoDescription() : '';
        $seoRobots = isset($page) ? ($page->meta_robots ?? 'index, follow') : 'index, follow';
        $ogImage   = isset($page) && $page->og_image ? asset($page->og_image) : null;
        $canonical = request()->url();

        $fontHeading = $settings->font_heading ?? 'Cormorant Garamond';
        $fontBody    = $settings->font_body ?? 'Montserrat';
        $accent      = $settings->accent_color ?? '#c9a96e';
    @endphp

    <title>{{ $seoTitle }}</title>
    @if($seoDesc)
    <meta name="description" content="{{ $seoDesc }}">
    @endif
    <meta name="robots" content="{{ $seoRobots }}">
    <link rel="canonical" href="{{ $canonical }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    @if($seoDesc)
    <meta property="og:description" content="{{ $seoDesc }}">
    @endif
    @if($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
    @endif

    @if(!empty($settings->favicon_path))
    <link rel="icon" href="{{ asset($settings->favicon_path) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/themes/sanzahra/style.css">

    <style>
        :root {
            --accent: {{ $accent }};
            --font-heading: '{{ $fontHeading }}', serif;
            --font-body: '{{ $fontBody }}', sans-serif;
        }
        body { font-family: var(--font-body); }
        h1,h2,h3,h4,h5,h6 { font-family: var(--font-heading); }
    </style>

    @if(!empty($settings->google_analytics_code))
        {!! $settings->google_analytics_code !!}
    @endif

    @if(!empty($settings->custom_head_code))
        {!! $settings->custom_head_code !!}
    @endif

    @if(isset($page) && !empty($page->custom_css))
    <style>{{ $page->custom_css }}</style>
    @endif

    @stack('styles')
</head>
<body @auth @can('edit-content') data-editor="1" @endcan @endauth>

@includeIf("themes.sanzahra.headers.{$headerType}", ['header' => $resolvedHeader])

<main>
    @yield('content')
</main>

@includeIf("themes.sanzahra.footers.{$footerType}", ['footer' => $resolvedFooter])

@stack('scripts')

@auth
    @can('edit-content')
        <script src="{{ asset('js/editor/overlay.js') }}"></script>
    @endcan
@endauth

@if(isset($page) && !empty($page->custom_js))
<script>{{ $page->custom_js }}</script>
@endif

@if(!empty($settings->custom_body_code))
    {!! $settings->custom_body_code !!}
@endif

</body>
</html>

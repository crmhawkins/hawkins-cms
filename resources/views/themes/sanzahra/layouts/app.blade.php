<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteName = config('app.name');
        $seoTitle = isset($page) ? ($page->seoTitle() . ' — ' . $siteName) : ($title ?? $siteName);
        $seoDesc  = isset($page) ? $page->seoDescription() : '';
        $seoRobots = isset($page) ? ($page->meta_robots ?? 'index, follow') : 'index, follow';
        $ogImage  = isset($page) && $page->og_image ? asset($page->og_image) : null;
        $canonical = request()->url();
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/themes/sanzahra/style.css">
    @stack('styles')
</head>
<body @auth @can('edit-content') data-editor="1" @endcan @endauth>

@php
    use App\Models\Header;
    use App\Models\Menu;
    use App\Models\MenuItem;

    $header       = Header::getInstance();
    $headerLayout = $header->layout ?? 'split';
    $bgColor      = $header->bg_color ?? '#ffffff';
    $textColor    = $header->text_color ?? '#000000';

    // Menu locations — fallback to all root items if no menu assigned
    $headerMenu  = Menu::forLocation('header');
    $menuItems   = $headerMenu
        ? $headerMenu->items()->whereNull('parent_id')->get()
        : MenuItem::whereNull('parent_id')->orderBy('sort')->get();

    $footerMenu  = Menu::forLocation('footer');
    $footerItems = $footerMenu
        ? $footerMenu->items()->whereNull('parent_id')->get()
        : collect();

    $half      = (int) ceil($menuItems->count() / 2);
    $leftItems = $menuItems->take($half);
    $rightItems = $menuItems->slice($half);
@endphp

<header style="background:{{ $bgColor }};color:{{ $textColor }};padding:.75rem 1.5rem;">
    @if($headerLayout === 'split')
        <nav style="display:flex;align-items:center;justify-content:space-between;max-width:1200px;margin:0 auto;gap:1rem;">
            <ul style="list-style:none;margin:0;padding:0;display:flex;gap:1.5rem;">
                @foreach($leftItems as $item)
                    <li><a href="{{ $item->url }}" style="color:{{ $textColor }};text-decoration:none;">{{ $item->label }}</a></li>
                @endforeach
            </ul>

            <a href="/" style="flex-shrink:0;">
                @if(!empty($header?->logo_path))
                    <img src="{{ asset($header->logo_path) }}" alt="{{ config('app.name') }}" style="height:40px;">
                @else
                    <span style="font-weight:700;font-size:1.25rem;color:{{ $textColor }};">{{ config('app.name') }}</span>
                @endif
            </a>

            <ul style="list-style:none;margin:0;padding:0;display:flex;gap:1.5rem;">
                @foreach($rightItems as $item)
                    <li><a href="{{ $item->url }}" style="color:{{ $textColor }};text-decoration:none;">{{ $item->label }}</a></li>
                @endforeach
            </ul>
        </nav>

    @elseif($headerLayout === 'logo_left')
        <nav style="display:flex;align-items:center;max-width:1200px;margin:0 auto;gap:2rem;">
            <a href="/" style="flex-shrink:0;">
                @if(!empty($header?->logo_path))
                    <img src="{{ asset($header->logo_path) }}" alt="{{ config('app.name') }}" style="height:40px;">
                @else
                    <span style="font-weight:700;font-size:1.25rem;color:{{ $textColor }};">{{ config('app.name') }}</span>
                @endif
            </a>
            <ul style="list-style:none;margin:0;padding:0;display:flex;gap:1.5rem;flex:1;">
                @foreach($menuItems as $item)
                    <li><a href="{{ $item->url }}" style="color:{{ $textColor }};text-decoration:none;">{{ $item->label }}</a></li>
                @endforeach
            </ul>
        </nav>

    @else {{-- logo_right --}}
        <nav style="display:flex;align-items:center;max-width:1200px;margin:0 auto;gap:2rem;justify-content:space-between;">
            <ul style="list-style:none;margin:0;padding:0;display:flex;gap:1.5rem;">
                @foreach($menuItems as $item)
                    <li><a href="{{ $item->url }}" style="color:{{ $textColor }};text-decoration:none;">{{ $item->label }}</a></li>
                @endforeach
            </ul>
            <a href="/" style="flex-shrink:0;">
                @if(!empty($header?->logo_path))
                    <img src="{{ asset($header->logo_path) }}" alt="{{ config('app.name') }}" style="height:40px;">
                @else
                    <span style="font-weight:700;font-size:1.25rem;color:{{ $textColor }};">{{ config('app.name') }}</span>
                @endif
            </a>
        </nav>
    @endif
</header>

<main>
    @yield('content')
</main>

<footer style="background:#f5f5f5;padding:1.5rem;text-align:center;color:#666;font-size:.875rem;margin-top:3rem;">
    @if($footerItems->isNotEmpty())
        <nav style="margin-bottom:.75rem;">
            <ul style="list-style:none;margin:0;padding:0;display:flex;flex-wrap:wrap;justify-content:center;gap:1.25rem;">
                @foreach($footerItems as $item)
                    <li><a href="{{ $item->url }}" style="color:#666;text-decoration:none;">{{ $item->label }}</a></li>
                @endforeach
            </ul>
        </nav>
    @endif
    &copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
</footer>

@stack('scripts')

@auth
    @can('edit-content')
        <script src="{{ asset('js/editor/overlay.js') }}"></script>
    @endcan
@endauth

</body>
</html>

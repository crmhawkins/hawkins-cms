@php
    $menu = \App\Models\Menu::forLocation('header');
    $items = $menu ? $menu->items()->orderBy('sort')->get() : collect();
@endphp
<header class="header-centered {{ $header->sticky ? 'header-sticky' : '' }}"
    style="background:{{ $header->bg_color ?? '#ffffff' }};color:{{ $header->text_color ?? '#111' }};">
    <div class="header-centered-logo">
        <a href="/">
            @if($header->logo_path)
                <img src="{{ asset($header->logo_path) }}" alt="{{ $header->logo_text ?? 'Logo' }}" style="height:{{ $header->logo_height ?? 60 }}px;">
            @else
                <span class="logo-text" style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;letter-spacing:.15em;">{{ $header->logo_text ?? config('app.name') }}</span>
            @endif
        </a>
    </div>
    <nav class="header-centered-nav" aria-label="Navegación principal">
        <ul class="nav-list nav-centered">
            @foreach($items as $item)
                <li><a href="{{ $item->url }}" style="color:{{ $header->text_color ?? '#111' }};">{{ $item->label }}</a></li>
            @endforeach
            @if($header->cta_text && $header->cta_url)
                <li><a href="{{ $header->cta_url }}" class="header-cta"
                       style="background:{{ $header->cta_bg_color ?? '#c9a96e' }};color:{{ $header->cta_text_color ?? '#fff' }};">
                       {{ $header->cta_text }}
                </a></li>
            @endif
        </ul>
    </nav>
    <button class="hamburger hamburger-centered" aria-label="Menú" onclick="document.querySelector('.header-centered-nav').classList.toggle('nav-open')">
        <span></span><span></span><span></span>
    </button>
</header>

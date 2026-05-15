@php
    $menu = \App\Models\Menu::forLocation('header');
    $items = $menu ? $menu->items()->orderBy('sort')->get() : collect();
@endphp
<header class="header-classic {{ $header->sticky ? 'header-sticky' : '' }} {{ $header->transparent_on_top ? 'header-transparent' : '' }}"
    style="background:{{ $header->bg_color ?? '#ffffff' }};color:{{ $header->text_color ?? '#111' }};">
    <div class="header-inner">
        {{-- Logo --}}
        <a href="/" class="header-logo">
            @if($header->logo_path)
                <img src="{{ asset($header->logo_path) }}" alt="{{ $header->logo_text ?? 'Logo' }}" style="height:{{ $header->logo_height ?? 50 }}px;display:block;">
            @else
                <span class="logo-text">{{ $header->logo_text ?? config('app.name') }}</span>
            @endif
        </a>

        {{-- Nav --}}
        <nav class="header-nav" aria-label="Navegación principal">
            <ul class="nav-list">
                @foreach($items as $item)
                    <li><a href="{{ $item->url }}" style="color:{{ $header->text_color ?? '#111' }};">{{ $item->label }}</a></li>
                @endforeach
            </ul>
        </nav>

        {{-- CTA + extras --}}
        <div class="header-actions">
            @if($header->cta_text && $header->cta_url)
                <a href="{{ $header->cta_url }}" class="header-cta"
                   style="background:{{ $header->cta_bg_color ?? '#111' }};color:{{ $header->cta_text_color ?? '#fff' }};">
                   {{ $header->cta_text }}
                </a>
            @endif
            {{-- Hamburger móvil --}}
            <button class="hamburger" aria-label="Menú" onclick="document.querySelector('.header-nav').classList.toggle('nav-open')">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

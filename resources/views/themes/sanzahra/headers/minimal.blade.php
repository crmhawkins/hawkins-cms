@php
    $menu = \App\Models\Menu::forLocation('header');
    $items = $menu ? $menu->items()->orderBy('sort')->get() : collect();
@endphp
<header class="header-minimal {{ $header->sticky ? 'header-sticky' : '' }} {{ $header->transparent_on_top ? 'header-transparent' : '' }}"
    style="background:{{ $header->transparent_on_top ? 'transparent' : ($header->bg_color ?? '#ffffff') }};color:{{ $header->text_color ?? '#111' }};">
    <div class="header-minimal-inner">
        <a href="/" class="header-logo">
            @if($header->logo_path)
                <img src="{{ asset($header->logo_path) }}" alt="{{ $header->logo_text ?? 'Logo' }}" style="height:{{ $header->logo_height ?? 45 }}px;">
            @else
                <span class="logo-text" style="font-size:1.4rem;letter-spacing:.1em;font-family:'Cormorant Garamond',serif;">{{ $header->logo_text ?? config('app.name') }}</span>
            @endif
        </a>
        <button class="hamburger hamburger-minimal" aria-label="Menú" onclick="document.getElementById('minimal-nav').classList.toggle('is-open')">
            <span style="background:{{ $header->text_color ?? '#111' }};"></span>
            <span style="background:{{ $header->text_color ?? '#111' }};"></span>
            <span style="background:{{ $header->text_color ?? '#111' }};"></span>
        </button>
    </div>
    {{-- Fullscreen overlay nav --}}
    <div id="minimal-nav" class="minimal-nav-overlay" style="background:{{ $header->bg_color ?? '#fff' }};">
        <button class="minimal-nav-close" onclick="document.getElementById('minimal-nav').classList.remove('is-open')" style="color:{{ $header->text_color ?? '#111' }};">✕</button>
        <ul class="minimal-nav-list">
            @foreach($items as $item)
                <li><a href="{{ $item->url }}" style="color:{{ $header->text_color ?? '#111' }};">{{ $item->label }}</a></li>
            @endforeach
            @if($header->cta_text && $header->cta_url)
                <li><a href="{{ $header->cta_url }}" class="minimal-cta"
                       style="background:{{ $header->cta_bg_color ?? '#c9a96e' }};color:{{ $header->cta_text_color ?? '#fff' }};">
                       {{ $header->cta_text }}
                </a></li>
            @endif
        </ul>
        @if($header->show_social)
            <div class="minimal-social">
                @if($header->social_instagram) <a href="{{ $header->social_instagram }}" style="color:{{ $header->text_color ?? '#111' }};">Instagram</a> @endif
                @if($header->social_facebook) <a href="{{ $header->social_facebook }}" style="color:{{ $header->text_color ?? '#111' }};">Facebook</a> @endif
                @if($header->social_twitter) <a href="{{ $header->social_twitter }}" style="color:{{ $header->text_color ?? '#111' }};">Twitter</a> @endif
            </div>
        @endif
    </div>
</header>

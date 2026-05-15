@php
    $menu = \App\Models\Menu::forLocation('header');
    $items = $menu ? $menu->items()->orderBy('sort')->get() : collect();
@endphp
<header class="header-mega {{ $header->sticky ? 'header-sticky' : '' }}"
    style="--header-bg:{{ $header->bg_color ?? '#fff' }};--header-text:{{ $header->text_color ?? '#111' }};">
    {{-- Top bar --}}
    <div class="header-mega-topbar">
        <div class="topbar-inner">
            <div class="topbar-contact">
                @if($header->phone)
                    <a href="tel:{{ preg_replace('/\s+/', '', $header->phone) }}">📞 {{ $header->phone }}</a>
                @endif
                @if($header->email)
                    <a href="mailto:{{ $header->email }}">✉️ {{ $header->email }}</a>
                @endif
            </div>
            @if($header->show_social)
                <div class="topbar-social">
                    @if($header->social_instagram) <a href="{{ $header->social_instagram }}" target="_blank" rel="noopener" title="Instagram">IG</a> @endif
                    @if($header->social_facebook) <a href="{{ $header->social_facebook }}" target="_blank" rel="noopener" title="Facebook">FB</a> @endif
                    @if($header->social_twitter) <a href="{{ $header->social_twitter }}" target="_blank" rel="noopener" title="Twitter">TW</a> @endif
                    @if($header->social_linkedin) <a href="{{ $header->social_linkedin }}" target="_blank" rel="noopener" title="LinkedIn">LI</a> @endif
                    @if($header->social_youtube) <a href="{{ $header->social_youtube }}" target="_blank" rel="noopener" title="YouTube">YT</a> @endif
                </div>
            @endif
        </div>
    </div>
    {{-- Main header --}}
    <div class="header-mega-main" style="background:{{ $header->bg_color ?? '#fff' }};">
        <div class="header-inner">
            <a href="/" class="header-logo">
                @if($header->logo_path)
                    <img src="{{ asset($header->logo_path) }}" alt="{{ $header->logo_text ?? 'Logo' }}" style="height:{{ $header->logo_height ?? 55 }}px;">
                @else
                    <span class="logo-text" style="font-family:'Cormorant Garamond',serif;font-size:1.7rem;letter-spacing:.1em;">{{ $header->logo_text ?? config('app.name') }}</span>
                @endif
            </a>
            <nav class="header-nav" aria-label="Navegación principal">
                <ul class="nav-list">
                    @foreach($items as $item)
                        <li><a href="{{ $item->url }}" style="color:{{ $header->text_color ?? '#111' }};">{{ $item->label }}</a></li>
                    @endforeach
                </ul>
            </nav>
            <div class="header-actions">
                @if($header->cta_text && $header->cta_url)
                    <a href="{{ $header->cta_url }}" class="header-cta"
                       style="background:{{ $header->cta_bg_color ?? '#c9a96e' }};color:{{ $header->cta_text_color ?? '#fff' }};">
                       {{ $header->cta_text }}
                    </a>
                @endif
                <button class="hamburger" aria-label="Menú" onclick="document.querySelector('.header-nav').classList.toggle('nav-open')">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </div>
</header>

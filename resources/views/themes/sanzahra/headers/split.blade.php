@php
    $menu = \App\Models\Menu::forLocation('header');
    $items = $menu ? $menu->items()->orderBy('sort')->get() : collect();
    $half = (int) ceil($items->count() / 2);
    $leftItems = $items->take($half);
    $rightItems = $items->skip($half);
@endphp
<header class="header-split {{ $header->sticky ? 'header-sticky' : '' }}"
    style="background:{{ $header->bg_color ?? '#ffffff' }};color:{{ $header->text_color ?? '#111' }};">
    <div class="header-split-inner">
        <nav class="nav-split-left">
            <ul class="nav-list">
                @foreach($leftItems as $item)
                    <li><a href="{{ $item->url }}" style="color:{{ $header->text_color ?? '#111' }};">{{ $item->label }}</a></li>
                @endforeach
            </ul>
        </nav>
        <a href="/" class="header-logo header-logo-center">
            @if($header->logo_path)
                <img src="{{ asset($header->logo_path) }}" alt="{{ $header->logo_text ?? 'Logo' }}" style="height:{{ $header->logo_height ?? 55 }}px;">
            @else
                <span class="logo-text" style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;letter-spacing:.12em;">{{ $header->logo_text ?? config('app.name') }}</span>
            @endif
        </a>
        <nav class="nav-split-right">
            <ul class="nav-list">
                @foreach($rightItems as $item)
                    <li><a href="{{ $item->url }}" style="color:{{ $header->text_color ?? '#111' }};">{{ $item->label }}</a></li>
                @endforeach
                @if($header->cta_text && $header->cta_url)
                    <li><a href="{{ $header->cta_url }}" class="header-cta"
                           style="background:{{ $header->cta_bg_color ?? '#111' }};color:{{ $header->cta_text_color ?? '#fff' }};">
                           {{ $header->cta_text }}
                    </a></li>
                @endif
            </ul>
        </nav>
        <button class="hamburger" aria-label="Menú" onclick="document.querySelector('.header-split').classList.toggle('mobile-open')">
            <span></span><span></span><span></span>
        </button>
    </div>
    {{-- Mobile nav --}}
    <div class="mobile-nav-panel">
        <ul class="nav-list-mobile">
            @foreach($items as $item)
                <li><a href="{{ $item->url }}">{{ $item->label }}</a></li>
            @endforeach
        </ul>
    </div>
</header>

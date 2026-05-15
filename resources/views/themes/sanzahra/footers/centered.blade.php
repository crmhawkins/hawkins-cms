@php
    $footerMenu = \App\Models\Menu::forLocation('footer');
    $footerItems = $footerMenu ? $footerMenu->items()->orderBy('sort')->get() : collect();
    $year = date('Y');
@endphp
<footer class="footer-centered" style="background:{{ $footer->bg_color ?? '#111' }};color:{{ $footer->text_color ?? '#fff' }};">
    <div class="footer-centered-inner">
        @if($footer->logo_path)
            <img src="{{ asset($footer->logo_path) }}" alt="" class="footer-logo">
        @else
            <span style="font-family:'Cormorant Garamond',serif;font-size:1.5rem;letter-spacing:.12em;display:block;margin-bottom:1rem;">
                {{ $footer->logo_text ?? config('app.name') }}
            </span>
        @endif
        @if($footer->tagline)
            <p style="color:{{ $footer->text_color ?? '#fff' }};opacity:.65;font-size:.9rem;margin-bottom:1.5rem;">{{ $footer->tagline }}</p>
        @endif
        <nav class="footer-centered-nav">
            @foreach($footerItems as $item)
                <a href="{{ $item->url }}" style="color:{{ $footer->link_color ?? '#c9a96e' }};">{{ $item->label }}</a>
            @endforeach
        </nav>
        <div class="footer-centered-social">
            @if($footer->social_instagram) <a href="{{ $footer->social_instagram }}" target="_blank" style="color:{{ $footer->link_color ?? '#c9a96e' }};">IG</a> @endif
            @if($footer->social_facebook) <a href="{{ $footer->social_facebook }}" target="_blank" style="color:{{ $footer->link_color ?? '#c9a96e' }};">FB</a> @endif
            @if($footer->social_twitter) <a href="{{ $footer->social_twitter }}" target="_blank" style="color:{{ $footer->link_color ?? '#c9a96e' }};">TW</a> @endif
            @if($footer->social_linkedin) <a href="{{ $footer->social_linkedin }}" target="_blank" style="color:{{ $footer->link_color ?? '#c9a96e' }};">LI</a> @endif
        </div>
        <p class="footer-copyright-text" style="color:{{ $footer->text_color ?? '#fff' }};opacity:.5;font-size:.8rem;margin-top:2rem;">
            {{ $footer->copyright_text ?? '© '.$year.' '.config('app.name') }}
        </p>
    </div>
</footer>

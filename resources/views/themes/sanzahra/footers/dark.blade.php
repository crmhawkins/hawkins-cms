@php
    $footerMenu = \App\Models\Menu::forLocation('footer');
    $footerItems = $footerMenu ? $footerMenu->items()->orderBy('sort')->get() : collect();
    $year = date('Y');
@endphp
<footer class="footer-dark" style="background:{{ $footer->bg_color ?? '#0a0a0a' }};color:{{ $footer->text_color ?? '#e0e0e0' }};">
    {{-- Newsletter --}}
    @if($footer->show_newsletter)
        <div class="footer-dark-newsletter" style="border-bottom:1px solid {{ $footer->border_color ?? '#222' }};">
            <div class="newsletter-inner">
                <h3 style="color:{{ $footer->text_color ?? '#fff' }};font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:300;">
                    {{ $footer->newsletter_title ?? 'Suscríbete a nuestro boletín' }}
                </h3>
                <form class="newsletter-form" onsubmit="return false;">
                    <input type="email" placeholder="{{ $footer->newsletter_placeholder ?? 'Tu email' }}"
                           style="background:#1a1a1a;border:1px solid {{ $footer->border_color ?? '#333' }};color:#fff;">
                    <button type="submit" style="background:{{ $footer->link_color ?? '#c9a96e' }};color:#fff;">Suscribir</button>
                </form>
            </div>
        </div>
    @endif
    {{-- Main --}}
    <div class="footer-inner footer-dark-main">
        <div class="footer-col">
            @if($footer->logo_path)
                <img src="{{ asset($footer->logo_path) }}" alt="" class="footer-logo">
            @else
                <span style="font-family:'Cormorant Garamond',serif;font-size:1.5rem;letter-spacing:.1em;color:#fff;">{{ $footer->logo_text ?? config('app.name') }}</span>
            @endif
            @if($footer->tagline) <p style="color:#888;margin-top:.75rem;font-size:.9rem;line-height:1.7;">{{ $footer->tagline }}</p> @endif
        </div>
        <div class="footer-col">
            <h4 style="color:{{ $footer->link_color ?? '#c9a96e' }};font-size:.75rem;letter-spacing:.15em;text-transform:uppercase;margin-bottom:1rem;">Menú</h4>
            <ul class="footer-links">
                @foreach($footerItems as $item)
                    <li><a href="{{ $item->url }}" style="color:#aaa;">{{ $item->label }}</a></li>
                @endforeach
            </ul>
        </div>
        <div class="footer-col">
            <h4 style="color:{{ $footer->link_color ?? '#c9a96e' }};font-size:.75rem;letter-spacing:.15em;text-transform:uppercase;margin-bottom:1rem;">Contacto</h4>
            <ul class="footer-contact-list" style="color:#aaa;">
                @if($footer->phone) <li>{{ $footer->phone }}</li> @endif
                @if($footer->email) <li>{{ $footer->email }}</li> @endif
                @if($footer->address) <li>{{ $footer->address }}</li> @endif
            </ul>
            <div class="footer-social" style="margin-top:1rem;">
                @if($footer->social_instagram) <a href="{{ $footer->social_instagram }}" style="color:{{ $footer->link_color ?? '#c9a96e' }};">IG</a> @endif
                @if($footer->social_facebook) <a href="{{ $footer->social_facebook }}" style="color:{{ $footer->link_color ?? '#c9a96e' }};">FB</a> @endif
                @if($footer->social_twitter) <a href="{{ $footer->social_twitter }}" style="color:{{ $footer->link_color ?? '#c9a96e' }};">TW</a> @endif
                @if($footer->social_linkedin) <a href="{{ $footer->social_linkedin }}" style="color:{{ $footer->link_color ?? '#c9a96e' }};">LI</a> @endif
            </div>
        </div>
    </div>
    <div class="footer-copyright" style="border-top:1px solid {{ $footer->border_color ?? '#222' }};color:#555;text-align:center;padding:1rem 2rem;font-size:.8rem;">
        {{ $footer->copyright_text ?? '© '.$year.' '.config('app.name').'. Todos los derechos reservados.' }}
    </div>
</footer>

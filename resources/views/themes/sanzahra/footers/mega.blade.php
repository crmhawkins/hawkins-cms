@php
    $footerMenu = \App\Models\Menu::forLocation('footer');
    $footerItems = $footerMenu ? $footerMenu->items()->orderBy('sort')->get() : collect();
    $year = date('Y');
    $cols = $footer->menu_columns ?? [];
@endphp
<footer class="footer-mega" style="background:{{ $footer->bg_color ?? '#0f0f0f' }};color:{{ $footer->text_color ?? '#ddd' }};">
    @if($footer->show_newsletter)
        <div class="footer-mega-newsletter" style="background:{{ $footer->link_color ?? '#c9a96e' }};">
            <div class="newsletter-mega-inner">
                <div>
                    <h3 style="color:#fff;font-size:1.4rem;font-weight:400;margin:0;">{{ $footer->newsletter_title ?? 'Mantente informado' }}</h3>
                </div>
                <form class="newsletter-mega-form" onsubmit="return false;">
                    <input type="email" placeholder="{{ $footer->newsletter_placeholder ?? 'Tu email' }}">
                    <button type="submit">Suscribir →</button>
                </form>
            </div>
        </div>
    @endif
    <div class="footer-mega-main">
        <div class="footer-mega-brand">
            @if($footer->logo_path)
                <img src="{{ asset($footer->logo_path) }}" alt="" class="footer-logo">
            @else
                <span style="font-family:'Cormorant Garamond',serif;font-size:1.7rem;letter-spacing:.1em;color:#fff;">{{ $footer->logo_text ?? config('app.name') }}</span>
            @endif
            @if($footer->tagline) <p style="color:#888;margin-top:.75rem;font-size:.9rem;line-height:1.7;max-width:260px;">{{ $footer->tagline }}</p> @endif
            <div class="footer-social" style="margin-top:1.25rem;">
                @if($footer->social_instagram) <a href="{{ $footer->social_instagram }}" style="color:{{ $footer->link_color ?? '#c9a96e' }};">IG</a> @endif
                @if($footer->social_facebook) <a href="{{ $footer->social_facebook }}" style="color:{{ $footer->link_color ?? '#c9a96e' }};">FB</a> @endif
                @if($footer->social_twitter) <a href="{{ $footer->social_twitter }}" style="color:{{ $footer->link_color ?? '#c9a96e' }};">TW</a> @endif
                @if($footer->social_linkedin) <a href="{{ $footer->social_linkedin }}" style="color:{{ $footer->link_color ?? '#c9a96e' }};">LI</a> @endif
            </div>
        </div>
        <div class="footer-mega-links">
            <div class="footer-col">
                <h4 style="color:{{ $footer->link_color ?? '#c9a96e' }};font-size:.7rem;letter-spacing:.18em;text-transform:uppercase;margin-bottom:1rem;">Páginas</h4>
                <ul class="footer-links">
                    @foreach($footerItems->take(5) as $item)
                        <li><a href="{{ $item->url }}" style="color:#999;">{{ $item->label }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="footer-col">
                <h4 style="color:{{ $footer->link_color ?? '#c9a96e' }};font-size:.7rem;letter-spacing:.18em;text-transform:uppercase;margin-bottom:1rem;">Contacto</h4>
                <ul class="footer-contact-list" style="color:#888;">
                    @if($footer->phone) <li><a href="tel:{{ preg_replace('/\s+/','',$footer->phone) }}" style="color:#999;">{{ $footer->phone }}</a></li> @endif
                    @if($footer->email) <li><a href="mailto:{{ $footer->email }}" style="color:#999;">{{ $footer->email }}</a></li> @endif
                    @if($footer->address) <li>{{ $footer->address }}</li> @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-mega-bottom" style="border-top:1px solid {{ $footer->border_color ?? '#222' }};">
        <p style="color:#555;font-size:.8rem;margin:0;">{{ $footer->copyright_text ?? '© '.$year.' '.config('app.name').'. Todos los derechos reservados.' }}</p>
        @foreach($footerItems->skip(5) as $item)
            <a href="{{ $item->url }}" style="color:#555;font-size:.8rem;">{{ $item->label }}</a>
        @endforeach
    </div>
</footer>

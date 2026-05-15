@php
    $footerMenu = \App\Models\Menu::forLocation('footer');
    $footerItems = $footerMenu ? $footerMenu->items()->orderBy('sort')->get() : collect();
    $half = (int) ceil($footerItems->count() / 2);
    $col1Items = $footerItems->take($half);
    $col2Items = $footerItems->skip($half);
    $year = date('Y');
@endphp
<footer class="footer-classic" style="background:{{ $footer->bg_color ?? '#111' }};color:{{ $footer->text_color ?? '#fff' }};">
    <div class="footer-inner">
        {{-- Col 1: Branding --}}
        <div class="footer-col">
            @if($footer->logo_path)
                <img src="{{ asset($footer->logo_path) }}" alt="{{ $footer->logo_text ?? 'Logo' }}" class="footer-logo">
            @else
                <span class="footer-logo-text" style="font-family:'Cormorant Garamond',serif;font-size:1.6rem;letter-spacing:.1em;color:{{ $footer->text_color ?? '#fff' }};">
                    {{ $footer->logo_text ?? config('app.name') }}
                </span>
            @endif
            @if($footer->tagline)
                <p class="footer-tagline" style="color:{{ $footer->text_color ?? '#fff' }};opacity:.7;margin-top:.75rem;line-height:1.6;font-size:.9rem;">{{ $footer->tagline }}</p>
            @endif
            {{-- Redes sociales --}}
            <div class="footer-social">
                @if($footer->social_instagram) <a href="{{ $footer->social_instagram }}" target="_blank" style="color:{{ $footer->link_color ?? '#c9a96e' }};" title="Instagram">Instagram</a> @endif
                @if($footer->social_facebook) <a href="{{ $footer->social_facebook }}" target="_blank" style="color:{{ $footer->link_color ?? '#c9a96e' }};" title="Facebook">Facebook</a> @endif
                @if($footer->social_twitter) <a href="{{ $footer->social_twitter }}" target="_blank" style="color:{{ $footer->link_color ?? '#c9a96e' }};" title="Twitter">Twitter</a> @endif
                @if($footer->social_linkedin) <a href="{{ $footer->social_linkedin }}" target="_blank" style="color:{{ $footer->link_color ?? '#c9a96e' }};" title="LinkedIn">LinkedIn</a> @endif
            </div>
        </div>
        {{-- Col 2: Menú --}}
        @if($col1Items->count())
        <div class="footer-col">
            <h4 class="footer-col-title" style="color:{{ $footer->text_color ?? '#fff' }};">Páginas</h4>
            <ul class="footer-links">
                @foreach($col1Items as $item)
                    <li><a href="{{ $item->url }}" style="color:{{ $footer->link_color ?? '#c9a96e' }};">{{ $item->label }}</a></li>
                @endforeach
            </ul>
        </div>
        @endif
        {{-- Col 3: Más links --}}
        @if($col2Items->count())
        <div class="footer-col">
            <h4 class="footer-col-title" style="color:{{ $footer->text_color ?? '#fff' }};">Información</h4>
            <ul class="footer-links">
                @foreach($col2Items as $item)
                    <li><a href="{{ $item->url }}" style="color:{{ $footer->link_color ?? '#c9a96e' }};">{{ $item->label }}</a></li>
                @endforeach
            </ul>
        </div>
        @endif
        {{-- Col 4: Contacto --}}
        <div class="footer-col">
            <h4 class="footer-col-title" style="color:{{ $footer->text_color ?? '#fff' }};">Contacto</h4>
            <ul class="footer-contact-list">
                @if($footer->phone) <li>📞 <a href="tel:{{ preg_replace('/\s+/','',$footer->phone) }}" style="color:{{ $footer->link_color ?? '#c9a96e' }};">{{ $footer->phone }}</a></li> @endif
                @if($footer->email) <li>✉️ <a href="mailto:{{ $footer->email }}" style="color:{{ $footer->link_color ?? '#c9a96e' }};">{{ $footer->email }}</a></li> @endif
                @if($footer->address) <li>📍 {{ $footer->address }}</li> @endif
            </ul>
        </div>
    </div>
    <div class="footer-copyright" style="border-top:1px solid {{ $footer->border_color ?? '#333' }};color:{{ $footer->text_color ?? '#fff' }};opacity:.6;">
        <p>{{ $footer->copyright_text ?? '© '.$year.' '.config('app.name').'. Todos los derechos reservados.' }}</p>
    </div>
</footer>

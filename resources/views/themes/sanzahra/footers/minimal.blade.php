@php
    $footerMenu = \App\Models\Menu::forLocation('footer');
    $footerItems = $footerMenu ? $footerMenu->items()->orderBy('sort')->get() : collect();
    $year = date('Y');
@endphp
<footer class="footer-minimal" style="background:{{ $footer->bg_color ?? '#f7f5f2' }};color:{{ $footer->text_color ?? '#666' }};border-top:1px solid {{ $footer->border_color ?? '#e8e4df' }};">
    <div class="footer-minimal-inner">
        <nav class="footer-minimal-nav">
            @foreach($footerItems as $item)
                <a href="{{ $item->url }}" style="color:{{ $footer->link_color ?? '#c9a96e' }};">{{ $item->label }}</a>
            @endforeach
        </nav>
        <p style="color:{{ $footer->text_color ?? '#999' }};font-size:.8rem;margin:0;">
            {{ $footer->copyright_text ?? '© '.$year.' '.config('app.name') }}
        </p>
    </div>
</footer>

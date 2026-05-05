@php $content = $block->content ?? []; @endphp
<section class="block-cta" style="padding:4rem 1rem;background:#333;color:#fff;text-align:center;">
    <div style="max-width:800px;margin:0 auto;">
        @if(!empty($content['title']))
            <h2 style="font-size:2.5rem;margin-bottom:1rem;">{{ $content['title'] }}</h2>
        @endif
        @if(!empty($content['subtitle']))
            <p style="font-size:1.2rem;margin-bottom:2rem;opacity:.85;">{{ $content['subtitle'] }}</p>
        @endif
        @if(!empty($content['button_text']) && !empty($content['button_url']))
            <a href="{{ $content['button_url'] }}"
               style="display:inline-block;padding:1rem 2.5rem;background:#fff;color:#333;border-radius:4px;font-weight:600;text-decoration:none;font-size:1rem;">
                {{ $content['button_text'] }}
            </a>
        @endif
    </div>
</section>

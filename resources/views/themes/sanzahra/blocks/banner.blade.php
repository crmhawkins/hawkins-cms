@php $content = $block->content ?? []; @endphp
@if(!empty($content['text']))
<div class="block-banner {{ !empty($content['dismissible']) ? 'banner-dismissible' : '' }}"
     id="banner-{{ $block->id }}"
     style="background:{{ $content['bg_color'] ?? '#c9a96e' }};color:{{ $content['text_color'] ?? '#fff' }};">
    <div class="banner-inner">
        <p class="banner-text">{{ $content['text'] }}</p>
        @if(!empty($content['cta_text']) && !empty($content['cta_url']))
            <a href="{{ $content['cta_url'] }}" class="banner-cta">{{ $content['cta_text'] }}</a>
        @endif
    </div>
    @if(!empty($content['dismissible']))
        <button class="banner-close" onclick="document.getElementById('banner-{{ $block->id }}').style.display='none';" title="Cerrar">✕</button>
    @endif
</div>
@endif

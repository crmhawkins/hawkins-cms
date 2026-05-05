@php $content = $block->content ?? []; @endphp
<section class="block-map" style="padding:0;">
    @if(!empty($content['embed_url']))
        <iframe
            src="{{ $content['embed_url'] }}"
            width="100%"
            height="400"
            style="border:0;display:block;"
            allowfullscreen
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="{{ $content['address'] ?? 'Mapa' }}"
        ></iframe>
    @else
        <div style="height:400px;background:#eee;display:flex;align-items:center;justify-content:center;color:#999;">
            Mapa no configurado
        </div>
    @endif
</section>

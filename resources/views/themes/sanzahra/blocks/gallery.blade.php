@php
    $content = $block->content ?? [];
    $images  = $content['images'] ?? [];
    $columns = (int) ($content['columns'] ?? 3);
@endphp
<section class="block-gallery" style="padding:3rem 1rem;">
    <div style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat({{ $columns }},1fr);gap:1rem;">
        @forelse($images as $src)
            <div style="overflow:hidden;border-radius:4px;">
                <img src="{{ $src }}" alt="" style="width:100%;height:220px;object-fit:cover;display:block;">
            </div>
        @empty
            <p style="color:#999;grid-column:1/-1;text-align:center;">Sin imágenes</p>
        @endforelse
    </div>
</section>

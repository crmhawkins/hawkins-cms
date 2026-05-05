@php
    $content = $block->content ?? [];
    $images  = $content['images'] ?? [];
    $columns = (int) ($content['columns'] ?? 3);
@endphp
<section class="block-gallery" style="padding:3rem 1rem;">
    @if(!empty($content['title']))
        <h2 style="text-align:center;margin-bottom:2rem;font-family:'Cormorant Garamond',serif;font-weight:400;font-size:2rem;">{{ $content['title'] }}</h2>
    @endif
    <div style="max-width:1200px;margin:0 auto;display:grid;grid-template-columns:repeat({{ $columns }},1fr);gap:1rem;">
        @forelse($images as $src)
            <div class="gallery-item" style="overflow:hidden;border-radius:2px;">
                <img src="{{ $src }}" alt="" style="width:100%;height:280px;object-fit:cover;display:block;transition:transform .4s ease;">
            </div>
        @empty
            <p style="color:#999;grid-column:1/-1;text-align:center;">Sin imágenes</p>
        @endforelse
    </div>
</section>

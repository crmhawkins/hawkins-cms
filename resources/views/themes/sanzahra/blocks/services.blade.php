@php
    $content = $block->content ?? [];
    $items   = $content['items'] ?? [];
@endphp
<section class="block-services" style="padding:3rem 1rem;">
    <div style="max-width:1200px;margin:0 auto;">
        @if(!empty($content['title']))
            <h2 style="text-align:center;margin-bottom:2.5rem;font-size:2rem;">{{ $content['title'] }}</h2>
        @endif
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:2rem;">
            @forelse($items as $item)
                <div style="text-align:center;padding:2rem;border:1px solid #eee;border-radius:8px;">
                    @if(!empty($item['icon']))
                        <div style="font-size:2.5rem;margin-bottom:1rem;">{{ $item['icon'] }}</div>
                    @endif
                    <h3 style="margin-bottom:.75rem;font-size:1.25rem;">{{ $item['title'] ?? '' }}</h3>
                    <p style="color:#666;line-height:1.6;">{{ $item['description'] ?? '' }}</p>
                </div>
            @empty
                <p style="color:#999;text-align:center;grid-column:1/-1;">Sin servicios configurados</p>
            @endforelse
        </div>
    </div>
</section>

@php
    $content  = $block->content ?? [];
    $position = $content['image_position'] ?? 'right';
    $flexDir  = $position === 'left' ? 'row-reverse' : 'row';
@endphp
<section class="block-text-image" style="padding:3rem 1rem;">
    <div style="max-width:1200px;margin:0 auto;display:flex;flex-wrap:wrap;gap:2rem;flex-direction:{{ $flexDir }};align-items:center;">
        <div style="flex:1;min-width:280px;">
            <h2 style="font-size:2rem;margin-bottom:1rem;">{{ $content['title'] ?? '' }}</h2>
            <div style="line-height:1.7;">{!! nl2br(e($content['body'] ?? '')) !!}</div>
        </div>
        @if(!empty($content['image']))
            <div style="flex:1;min-width:280px;">
                <img src="{{ $content['image'] }}" alt="{{ $content['title'] ?? '' }}" style="width:100%;border-radius:8px;display:block;">
            </div>
        @endif
    </div>
</section>

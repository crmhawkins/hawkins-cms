@php $content = $block->content ?? []; @endphp
<div class="block-divider" style="padding:{{ $content['padding'] ?? 20 }}px 2rem;">
    <div style="
        max-width: {{ $content['max_width'] ?? '800px' }};
        margin: 0 auto;
    ">
        @if(($content['style'] ?? 'solid') === 'dots')
            <div style="text-align:center;letter-spacing:.5em;color:{{ $content['color'] ?? '#e0dbd5' }};font-size:1.2rem;">• • • • •</div>
        @elseif(($content['style'] ?? 'solid') === 'asterisk')
            <div style="text-align:center;color:{{ $content['color'] ?? '#c9a96e' }};font-size:1.5rem;letter-spacing:.3em;">✦ ✦ ✦</div>
        @else
            <hr style="
                border:none;
                border-top: {{ $content['thickness'] ?? 1 }}px {{ $content['style'] ?? 'solid' }} {{ $content['color'] ?? '#e0dbd5' }};
                width: {{ $content['width'] ?? '100%' }};
                margin: 0 auto;
            ">
        @endif
    </div>
</div>

@php $content = $block->content ?? []; @endphp
@if(!empty($content['src']))
    <div class="block-image" style="text-align:{{ $content['align'] ?? 'center' }};padding:2rem;">
        @if(!empty($content['link_url']))
            <a href="{{ $content['link_url'] }}" @if(!empty($content['link_new_tab'])) target="_blank" rel="noopener" @endif>
        @endif

        <figure style="display:inline-block;margin:0;max-width:{{ $content['max_width'] ?? '100%' }};">
            <img
                src="{{ $content['src'] }}"
                alt="{{ $content['alt'] ?? '' }}"
                style="
                    width:100%;
                    max-width:{{ $content['max_width'] ?? '100%' }};
                    border-radius:{{ $content['border_radius'] ?? '0' }}px;
                    @if(!empty($content['shadow'])) box-shadow:0 8px 32px rgba(0,0,0,.15); @endif
                    display:block;
                "
            >
            @if(!empty($content['caption']))
                <figcaption style="text-align:center;font-size:.85rem;color:#999;margin-top:.75rem;font-style:italic;">
                    {{ $content['caption'] }}
                </figcaption>
            @endif
        </figure>

        @if(!empty($content['link_url'])) </a> @endif
    </div>
@endif

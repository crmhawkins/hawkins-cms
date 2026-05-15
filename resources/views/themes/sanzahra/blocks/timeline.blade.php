@php $content = $block->content ?? []; $items = $content['items'] ?? []; @endphp
<section class="block-timeline">
    <div class="timeline-inner">
        @if(!empty($content['title']))<h2 class="block-title">{{ $content['title'] }}</h2>@endif
        <div class="timeline-list">
            @foreach($items as $item)
                <div class="timeline-item">
                    <div class="timeline-year">{{ $item['year'] ?? '' }}</div>
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <h3 class="timeline-title">{{ $item['title'] ?? '' }}</h3>
                        @if(!empty($item['description']))<p class="timeline-desc">{{ $item['description'] }}</p>@endif
                        @if(!empty($item['image']))<img src="{{ $item['image'] }}" alt="{{ $item['title'] ?? '' }}" class="timeline-img">@endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

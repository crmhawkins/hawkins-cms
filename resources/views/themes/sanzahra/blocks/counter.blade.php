@php $content = $block->content ?? []; $items = $content['items'] ?? []; @endphp
<section class="block-counter" style="background:{{ $content['bg_color'] ?? '#111' }};">
    @if(!empty($content['title']))
        <h2 class="block-title" style="color:#fff;">{{ $content['title'] }}</h2>
    @endif
    <div class="counter-grid">
        @foreach($items as $item)
            <div class="counter-item">
                @if(!empty($item['icon']))<div class="counter-icon">{{ $item['icon'] }}</div>@endif
                <div class="counter-number">{{ $item['number'] ?? '0' }}</div>
                <div class="counter-label">{{ $item['label'] ?? '' }}</div>
            </div>
        @endforeach
    </div>
</section>

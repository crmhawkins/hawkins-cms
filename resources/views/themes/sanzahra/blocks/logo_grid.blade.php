@php $content = $block->content ?? []; $logos = $content['logos'] ?? []; @endphp
<section class="block-logo-grid">
    <div class="logo-grid-inner">
        @if(!empty($content['title']))<h2 class="block-title">{{ $content['title'] }}</h2>@endif
        @if(!empty($content['subtitle']))<p class="block-subtitle">{{ $content['subtitle'] }}</p>@endif
        <div class="logo-grid">
            @foreach($logos as $logo)
                @if(!empty($logo['url']))
                    <a href="{{ $logo['url'] }}" target="_blank" rel="noopener" class="logo-grid-item">
                        <img src="{{ $logo['image'] }}" alt="{{ $logo['alt'] ?? '' }}">
                    </a>
                @else
                    <div class="logo-grid-item">
                        <img src="{{ $logo['image'] }}" alt="{{ $logo['alt'] ?? '' }}">
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>

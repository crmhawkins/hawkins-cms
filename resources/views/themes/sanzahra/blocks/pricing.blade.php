@php $content = $block->content ?? []; $plans = $content['plans'] ?? []; @endphp
<section class="block-pricing">
    <div class="pricing-inner">
        @if(!empty($content['title']))<h2 class="block-title">{{ $content['title'] }}</h2>@endif
        @if(!empty($content['subtitle']))<p class="block-subtitle">{{ $content['subtitle'] }}</p>@endif
        <div class="pricing-grid">
            @foreach($plans as $plan)
                <div class="pricing-card {{ !empty($plan['highlighted']) ? 'pricing-highlighted' : '' }}">
                    @if(!empty($plan['highlighted']))<div class="pricing-badge">Más popular</div>@endif
                    <h3 class="pricing-name">{{ $plan['name'] ?? '' }}</h3>
                    <div class="pricing-price">{{ $plan['price'] ?? '' }}</div>
                    @if(!empty($plan['description']))<p class="pricing-desc">{{ $plan['description'] }}</p>@endif
                    @if(!empty($plan['features']))
                        <ul class="pricing-features">
                            @foreach(explode("\n", trim($plan['features'])) as $feature)
                                @if(trim($feature))<li>✓ {{ trim($feature) }}</li>@endif
                            @endforeach
                        </ul>
                    @endif
                    @if(!empty($plan['cta_text']) && !empty($plan['cta_url']))
                        <a href="{{ $plan['cta_url'] }}" class="pricing-cta {{ !empty($plan['highlighted']) ? 'pricing-cta-primary' : '' }}">
                            {{ $plan['cta_text'] }}
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

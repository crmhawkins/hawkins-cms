@php $content = $block->content ?? []; $items = $content['items'] ?? []; @endphp
<section class="block-testimonials">
    @if(!empty($content['title']))
        <h2 class="block-title">{{ $content['title'] }}</h2>
    @endif
    <div class="testimonials-grid">
        @forelse($items as $item)
            <div class="testimonial-card">
                @if(!empty($item['photo']))
                    <img src="{{ $item['photo'] }}" alt="{{ $item['name'] ?? '' }}" class="testimonial-photo">
                @else
                    <div class="testimonial-avatar">{{ mb_substr($item['name'] ?? 'A', 0, 1) }}</div>
                @endif
                @if(!empty($item['rating']))
                    <div class="testimonial-stars">{{ str_repeat('★', (int)$item['rating']) }}{{ str_repeat('☆', max(0, 5-(int)$item['rating'])) }}</div>
                @endif
                <blockquote class="testimonial-text">"{{ $item['text'] ?? '' }}"</blockquote>
                <cite class="testimonial-author">
                    <strong>{{ $item['name'] ?? '' }}</strong>
                    @if(!empty($item['role'])) <span>{{ $item['role'] }}</span> @endif
                </cite>
            </div>
        @empty
            <p style="text-align:center;color:#999;">Sin testimonios configurados</p>
        @endforelse
    </div>
</section>

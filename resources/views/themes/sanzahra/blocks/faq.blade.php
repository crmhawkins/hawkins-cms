@php $content = $block->content ?? []; $items = $content['items'] ?? []; @endphp
<section class="block-faq">
    <div class="faq-inner">
        @if(!empty($content['title']))
            <h2 class="block-title">{{ $content['title'] }}</h2>
        @endif
        @if(!empty($content['subtitle']))
            <p class="block-subtitle">{{ $content['subtitle'] }}</p>
        @endif
        <div class="faq-list">
            @foreach($items as $i => $item)
                <div class="faq-item" id="faq-{{ $block->id }}-{{ $i }}">
                    <button class="faq-question" onclick="
                        var el=document.getElementById('faq-ans-{{ $block->id }}-{{ $i }}');
                        var btn=this;
                        el.style.display=el.style.display==='none'?'block':'none';
                        btn.classList.toggle('faq-open');
                    ">
                        {{ $item['question'] ?? '' }}
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer" id="faq-ans-{{ $block->id }}-{{ $i }}" style="display:none;">
                        <p>{{ $item['answer'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

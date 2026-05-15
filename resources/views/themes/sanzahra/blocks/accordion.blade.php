@php $content = $block->content ?? []; $items = $content['items'] ?? []; @endphp
<section class="block-accordion">
    <div class="accordion-inner">
        @if(!empty($content['title']))
            <h2 class="block-title">{{ $content['title'] }}</h2>
        @endif
        @foreach($items as $i => $item)
            @php $open = !empty($item['open']); @endphp
            <div class="accordion-item {{ $open ? 'accordion-open' : '' }}">
                <button class="accordion-heading" onclick="
                    var p=this.parentElement;
                    p.classList.toggle('accordion-open');
                    var body=p.querySelector('.accordion-body');
                    body.style.maxHeight=p.classList.contains('accordion-open')?(body.scrollHeight+'px'):'0';
                ">
                    {{ $item['heading'] ?? '' }}
                    <span class="accordion-arrow">↓</span>
                </button>
                <div class="accordion-body" style="max-height:{{ $open ? '500px' : '0' }};">
                    <div class="accordion-body-inner">{{ $item['body'] ?? '' }}</div>
                </div>
            </div>
        @endforeach
    </div>
</section>

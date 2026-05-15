@php $content = $block->content ?? []; @endphp
@if(!empty($content['body']))
    <div class="block-text" style="padding:{{ ($block->padding_top ?: 40) }}px 2rem {{ ($block->padding_bottom ?: 40) }}px;">
        @if(!empty($content['title']))
            <h2 class="block-title" @if(!empty($content['title_align'])) style="text-align:{{ $content['title_align'] }}" @endif>
                {{ $content['title'] }}
            </h2>
        @endif
        <div class="block-text-body prose" style="
            max-width: {{ ['normal'=>'760px','wide'=>'1000px','narrow'=>'600px','full'=>'100%'][$content['max_width'] ?? 'normal'] }};
            margin: 0 auto;
            text-align: {{ $content['text_align'] ?? 'left' }};
            font-size: {{ $content['font_size'] ?? '1' }}rem;
            line-height: {{ $content['line_height'] ?? '1.75' }};
            color: {{ $content['text_color'] ?? 'inherit' }};
        ">
            {!! nl2br(e($content['body'])) !!}
        </div>
    </div>
@endif

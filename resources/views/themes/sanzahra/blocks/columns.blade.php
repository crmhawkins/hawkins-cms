@php
    $content = $block->content ?? [];
    $cols    = $content['columns'] ?? [];
    $count   = count($cols) ?: 2;
    $gap     = $content['gap'] ?? '2rem';
    $valign  = $content['vertical_align'] ?? 'top';
@endphp
<div class="block-columns" style="padding:3rem 2rem;">
    <div style="
        display: grid;
        grid-template-columns: repeat({{ $count }}, 1fr);
        gap: {{ $gap }};
        align-items: {{ $valign }};
        max-width: 1200px;
        margin: 0 auto;
    ">
        @foreach($cols as $col)
            <div class="block-col" style="
                text-align: {{ $col['text_align'] ?? 'left' }};
                padding: {{ $col['padding'] ?? '0' }};
                background: {{ $col['bg_color'] ?? 'transparent' }};
                border-radius: {{ $col['border_radius'] ?? '0' }}px;
            ">
                @if(!empty($col['image']))
                    <img src="{{ $col['image'] }}" alt="{{ $col['image_alt'] ?? '' }}"
                         style="width:100%;border-radius:8px;margin-bottom:1rem;display:block;">
                @endif
                @if(!empty($col['title']))
                    <h3 style="margin-bottom:.75rem;font-size:1.4rem;font-family:'Cormorant Garamond',serif;">{{ $col['title'] }}</h3>
                @endif
                @if(!empty($col['body']))
                    <div style="color:#666;line-height:1.7;font-size:.95rem;">{!! nl2br(e($col['body'])) !!}</div>
                @endif
                @if(!empty($col['button_text']) && !empty($col['button_url']))
                    <a href="{{ $col['button_url'] }}" style="
                        display:inline-block;
                        margin-top:1.25rem;
                        padding:.7rem 1.5rem;
                        background:#111;
                        color:#fff;
                        text-decoration:none;
                        font-size:.8rem;
                        letter-spacing:.08em;
                        text-transform:uppercase;
                        border-radius:4px;
                    ">{{ $col['button_text'] }}</a>
                @endif
            </div>
        @endforeach
    </div>
</div>

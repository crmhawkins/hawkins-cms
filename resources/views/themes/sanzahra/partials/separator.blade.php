@php
    $type     = $type ?? 'wave';
    $color    = $color ?? '#ffffff';
    $position = $position ?? 'bottom'; // top o bottom
    $flip     = $position === 'top' ? 'transform:scaleY(-1);' : '';
@endphp

<div class="cms-separator cms-separator-{{ $type }}" style="line-height:0;overflow:hidden;{{ $flip }}">
    @if($type === 'wave')
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" style="width:100%;height:80px;display:block;">
            <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="{{ $color }}"/>
        </svg>
    @elseif($type === 'diagonal')
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" style="width:100%;height:80px;display:block;">
            <polygon points="0,80 1440,0 1440,80" fill="{{ $color }}"/>
        </svg>
    @elseif($type === 'curve')
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" style="width:100%;height:80px;display:block;">
            <path d="M0,80 Q720,0 1440,80 L1440,80 L0,80 Z" fill="{{ $color }}"/>
        </svg>
    @elseif($type === 'triangle')
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" style="width:100%;height:80px;display:block;">
            <polygon points="0,80 720,0 1440,80" fill="{{ $color }}"/>
        </svg>
    @endif
</div>

@php $content = $block->content ?? []; $height = (int)($content['height'] ?? 60); @endphp
<div class="block-spacer" style="height:{{ $height }}px;width:100%;"></div>

@php $content = $block->content ?? []; @endphp
<section class="hero-section" style="position:relative;min-height:60vh;display:flex;align-items:center;justify-content:center;overflow:hidden;background-image:url('{{ $content['background_image'] ?? ($content['image'] ?? '') }}');background-size:cover;background-position:center;">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,0.35);z-index:1;"></div>
    <div style="position:relative;z-index:2;text-align:center;color:#fff;padding:2rem;">
        <x-editable-text field="title" :blockId="$block->id" tag="h1" style="font-size:3rem;margin:0 0 1rem;font-family:'Cormorant Garamond',serif;font-weight:300;letter-spacing:.1em;">
            {{ $content['title'] ?? 'Título principal' }}
        </x-editable-text>
        <x-editable-text field="subtitle" :blockId="$block->id" tag="p" style="font-size:1.25rem;margin:0;font-family:'Montserrat',sans-serif;font-weight:300;letter-spacing:.05em;">
            {{ $content['subtitle'] ?? '' }}
        </x-editable-text>
    </div>
</section>

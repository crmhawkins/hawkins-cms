@php $content = $block->content ?? []; @endphp
<section class="hero-section" style="position:relative;min-height:60vh;display:flex;align-items:center;justify-content:center;overflow:hidden;">
    <x-editable-image
        field="background_image"
        :blockId="$block->id"
        tag="div"
        class="hero-bg"
        style="position:absolute;inset:0;background-image:url('{{ $content['background_image'] ?? '' }}');background-size:cover;background-position:center;"
    />
    <div style="position:relative;z-index:2;text-align:center;color:#fff;padding:2rem;">
        <x-editable-text field="title" :blockId="$block->id" tag="h1" style="font-size:3rem;margin:0 0 1rem;">
            {{ $content['title'] ?? 'Título principal' }}
        </x-editable-text>
        <x-editable-text field="subtitle" :blockId="$block->id" tag="p" style="font-size:1.25rem;margin:0;">
            {{ $content['subtitle'] ?? 'Subtítulo' }}
        </x-editable-text>
    </div>
</section>

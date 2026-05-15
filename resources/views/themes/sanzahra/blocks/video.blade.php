@php
    $content = $block->content ?? [];
    $url = $content['video_url'] ?? '';
    // Convertir URL YouTube/Vimeo a embed
    if (str_contains($url, 'youtube.com/watch')) {
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        $embedUrl = 'https://www.youtube.com/embed/'.($params['v'] ?? '');
    } elseif (str_contains($url, 'youtu.be/')) {
        $embedUrl = 'https://www.youtube.com/embed/'.basename(parse_url($url, PHP_URL_PATH));
    } elseif (str_contains($url, 'vimeo.com/')) {
        $embedUrl = 'https://player.vimeo.com/video/'.basename(parse_url($url, PHP_URL_PATH));
    } else {
        $embedUrl = $url;
    }
    if (!empty($content['autoplay'])) $embedUrl .= '?autoplay=1&mute=1';
@endphp
<section class="block-video">
    <div class="video-inner">
        @if(!empty($content['title']))
            <h2 class="block-title">{{ $content['title'] }}</h2>
        @endif
        @if(!empty($content['subtitle']))
            <p class="block-subtitle">{{ $content['subtitle'] }}</p>
        @endif
        @if($embedUrl)
            <div class="video-wrapper">
                <iframe src="{{ $embedUrl }}" frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" loading="lazy"></iframe>
            </div>
        @endif
    </div>
</section>

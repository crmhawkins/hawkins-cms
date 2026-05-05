@extends('themes.sanzahra.layouts.app')

@section('content')
<article style="max-width:800px;margin:0 auto;padding:3rem 1.5rem;">

    {{-- Back link --}}
    <a href="{{ route('blog.index') }}"
       style="font-family:'Montserrat',sans-serif;font-size:.75rem;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:#888;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;margin-bottom:2rem;">
        ← Volver al blog
    </a>

    {{-- Category --}}
    @if($post->category)
    <div style="margin-bottom:.75rem;">
        <a href="{{ route('blog.index') }}"
           style="font-family:'Montserrat',sans-serif;font-size:.7rem;font-weight:500;text-transform:uppercase;letter-spacing:.12em;color:#888;text-decoration:none;">
            {{ $post->category->name }}
        </a>
    </div>
    @endif

    {{-- Title --}}
    <h1 style="font-family:'Cormorant Garamond',serif;font-size:2.75rem;font-weight:300;line-height:1.2;margin:0 0 1rem;letter-spacing:.03em;">
        {{ $post->title }}
    </h1>

    {{-- Date --}}
    @if($post->published_at)
    <time style="font-family:'Montserrat',sans-serif;font-size:.8rem;color:#aaa;display:block;margin-bottom:2rem;">
        {{ $post->published_at->format('d \d\e F \d\e Y') }}
    </time>
    @endif

    {{-- Featured image --}}
    @if($post->featured_image)
    <img src="{{ asset('storage/' . $post->featured_image) }}"
         alt="{{ $post->title }}"
         style="width:100%;max-height:480px;object-fit:cover;display:block;margin-bottom:2.5rem;">
    @endif

    {{-- Body --}}
    <div style="font-family:'Montserrat',sans-serif;font-size:.95rem;line-height:1.8;color:#333;">
        {!! $post->body !!}
    </div>

    {{-- Back link bottom --}}
    <div style="margin-top:3rem;padding-top:2rem;border-top:1px solid #e8e8e8;">
        <a href="{{ route('blog.index') }}"
           style="font-family:'Montserrat',sans-serif;font-size:.75rem;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:#333;text-decoration:none;border-bottom:1px solid #333;padding-bottom:1px;">
            ← Volver al blog
        </a>
    </div>

</article>
@endsection

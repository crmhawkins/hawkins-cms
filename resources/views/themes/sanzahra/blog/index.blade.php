@extends('themes.sanzahra.layouts.app')

@section('content')
<div style="max-width:1200px;margin:0 auto;padding:3rem 1.5rem;">
    <h1 style="font-family:'Cormorant Garamond',serif;font-size:2.5rem;font-weight:300;margin-bottom:2.5rem;text-align:center;letter-spacing:.05em;">Blog</h1>

    @if($posts->isEmpty())
        <p style="text-align:center;color:#999;">No hay entradas publicadas aún.</p>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:2rem;">
            @foreach($posts as $post)
            <article style="background:#fff;border:1px solid #e8e8e8;overflow:hidden;">
                @if($post->featured_image)
                <a href="{{ route('blog.show', $post->slug) }}">
                    <img src="{{ asset('storage/' . $post->featured_image) }}"
                         alt="{{ $post->title }}"
                         style="width:100%;height:220px;object-fit:cover;display:block;">
                </a>
                @endif
                <div style="padding:1.5rem;">
                    @if($post->category)
                    <span style="font-family:'Montserrat',sans-serif;font-size:.7rem;font-weight:500;text-transform:uppercase;letter-spacing:.12em;color:#888;display:block;margin-bottom:.5rem;">
                        {{ $post->category->name }}
                    </span>
                    @endif
                    <h2 style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;font-weight:400;margin:0 0 .75rem;">
                        <a href="{{ route('blog.show', $post->slug) }}" style="color:inherit;text-decoration:none;">
                            {{ $post->title }}
                        </a>
                    </h2>
                    @if($post->excerpt)
                    <p style="font-family:'Montserrat',sans-serif;font-size:.875rem;color:#555;line-height:1.6;margin:0 0 1rem;">
                        {{ Str::limit($post->excerpt, 120) }}
                    </p>
                    @endif
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:1rem;">
                        @if($post->published_at)
                        <time style="font-family:'Montserrat',sans-serif;font-size:.75rem;color:#aaa;">
                            {{ $post->published_at->format('d/m/Y') }}
                        </time>
                        @endif
                        <a href="{{ route('blog.show', $post->slug) }}"
                           style="font-family:'Montserrat',sans-serif;font-size:.75rem;font-weight:500;text-transform:uppercase;letter-spacing:.1em;color:#333;text-decoration:none;border-bottom:1px solid #333;padding-bottom:1px;">
                            Leer más
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <div style="margin-top:3rem;display:flex;justify-content:center;gap:.5rem;">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection

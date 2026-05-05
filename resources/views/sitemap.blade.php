<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($pages as $page)
<url>
  <loc>{{ url('/' . $page->slug) }}</loc>
  <lastmod>{{ $page->updated_at->toAtomString() }}</lastmod>
  <changefreq>weekly</changefreq>
</url>
@endforeach
@foreach($posts as $post)
<url>
  <loc>{{ route('blog.show', $post->slug) }}</loc>
  <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
  <changefreq>monthly</changefreq>
</url>
@endforeach
</urlset>

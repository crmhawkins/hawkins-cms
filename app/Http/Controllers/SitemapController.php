<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;

class SitemapController extends Controller
{
    public function index()
    {
        $pages = Page::where('status', 'published')->get();
        $posts = Post::published()->get();
        return response()->view('sitemap', compact('pages', 'posts'))
            ->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $content = "User-agent: *\nAllow: /\nSitemap: " . url('/sitemap.xml') . "\n";
        return response($content)->header('Content-Type', 'text/plain');
    }
}

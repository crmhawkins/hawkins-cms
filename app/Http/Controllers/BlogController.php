<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SiteSettings;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::published()->with('category')->latest('published_at')->paginate(12);
        $settings = SiteSettings::instance();
        $title = 'Blog';
        return view('themes.sanzahra.blog.index', compact('posts', 'settings', 'title'));
    }

    public function show(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        $settings = SiteSettings::instance();
        // Pass $post as $page so layout SEO block picks up seoTitle/seoDescription/meta_robots/og_image
        $page = $post;
        return view('themes.sanzahra.blog.show', compact('post', 'settings', 'page'));
    }
}

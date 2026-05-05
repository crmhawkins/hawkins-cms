<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\SiteSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Cache::remember("page:{$slug}", now()->addHour(), function () use ($slug) {
            return Page::where('slug', $slug)
                ->where('status', 'published')
                ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
                ->with(['blocks' => fn ($q) => $q->where('active', true)->orderBy('sort')])
                ->firstOrFail();
        });

        $theme = SiteSettings::instance()->theme ?? 'sanzahra';

        return view("themes.{$theme}.page", compact('page'));
    }

    public function home(): View
    {
        $page = Cache::remember('page:home', now()->addHour(), function () {
            return Page::where('slug', 'home')
                ->where('status', 'published')
                ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
                ->with(['blocks' => fn ($q) => $q->where('active', true)->orderBy('sort')])
                ->firstOrFail();
        });

        $theme = SiteSettings::instance()->theme ?? 'sanzahra';

        return view("themes.{$theme}.page", compact('page'));
    }
}

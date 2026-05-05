<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\SiteSettings;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->with(['blocks' => fn ($q) => $q->orderBy('sort')])
            ->firstOrFail();

        $theme = SiteSettings::instance()->theme ?? 'sanzahra';

        return view("themes.{$theme}.page", compact('page'));
    }

    public function home(): View
    {
        $page = Page::where('slug', 'home')
            ->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->with(['blocks' => fn ($q) => $q->orderBy('sort')])
            ->firstOrFail();

        $theme = SiteSettings::instance()->theme ?? 'sanzahra';

        return view("themes.{$theme}.page", compact('page'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::where('slug', $slug)
            ->where('tenant_id', tenant('id'))
            ->where('status', 'published')
            ->with(['blocks' => fn ($q) => $q->orderBy('sort')])
            ->firstOrFail();

        $theme = $page->tenant->theme ?? 'sanzahra';

        return view("themes.{$theme}.page", compact('page'));
    }

    public function home(): View|RedirectResponse
    {
        $page = Page::where('tenant_id', tenant('id'))
            ->where('slug', 'home')
            ->where('status', 'published')
            ->with(['blocks' => fn ($q) => $q->orderBy('sort')])
            ->first();

        if (! $page) {
            abort(404);
        }

        $theme = $page->tenant->theme ?? 'sanzahra';

        return view("themes.{$theme}.page", compact('page'));
    }
}

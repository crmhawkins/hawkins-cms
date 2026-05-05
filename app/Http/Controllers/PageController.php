<?php
namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Response;

class PageController extends Controller
{
    public function __invoke(string $slug): Response
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->with('blocks')
            ->firstOrFail();

        $theme = function_exists('tenant') && tenant()
            ? (tenant()->theme ?? 'sanzahra')
            : 'sanzahra';

        return response()->view("themes.{$theme}.page", compact('page'));
    }
}

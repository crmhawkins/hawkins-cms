<?php

namespace App\Observers;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class PageObserver
{
    public function saved(Page $page): void
    {
        Cache::forget("page:{$page->slug}");
        Cache::forget('page:home');
    }

    public function deleted(Page $page): void
    {
        Cache::forget("page:{$page->slug}");
        Cache::forget('page:home');
    }
}

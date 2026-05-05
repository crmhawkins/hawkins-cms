<?php

namespace App\Observers;

use App\Models\Block;
use Illuminate\Support\Facades\Cache;

class BlockObserver
{
    public function saved(Block $block): void
    {
        if ($block->page) {
            Cache::forget("page:{$block->page->slug}");
        }
        Cache::forget('page:home');
    }
}

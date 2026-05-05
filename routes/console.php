<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    // Keep only the 20 most recent revisions per block
    $blockIds = DB::table('block_revisions')
        ->select('block_id')
        ->groupBy('block_id')
        ->havingRaw('COUNT(*) > 20')
        ->pluck('block_id');

    foreach ($blockIds as $blockId) {
        $keepIds = DB::table('block_revisions')
            ->where('block_id', $blockId)
            ->orderByDesc('id')
            ->limit(20)
            ->pluck('id');

        DB::table('block_revisions')
            ->where('block_id', $blockId)
            ->whereNotIn('id', $keepIds)
            ->delete();
    }
})->daily()->name('revisions:prune')->withoutOverlapping();

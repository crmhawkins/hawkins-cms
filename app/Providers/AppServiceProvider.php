<?php

namespace App\Providers;

use App\Models\Block;
use App\Models\User;
use App\Policies\BlockPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Block::class, BlockPolicy::class);

        Gate::define('edit-content', function (User $user) {
            return $user->hasRole(['admin', 'editor', 'superadmin']);
        });

        \App\Models\Page::observe(\App\Observers\PageObserver::class);
        \App\Models\Block::observe(\App\Observers\BlockObserver::class);
    }
}

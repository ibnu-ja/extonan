<?php

namespace App\Providers;

use App\Models\BasePost;
use App\Policies\PostPolicy;
use Gate;
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
        Gate::policy(BasePost::class, PostPolicy::class);
    }
}

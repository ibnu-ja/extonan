<?php

namespace App\Providers;

use App\Models\Post;
use Illuminate\Support\ServiceProvider;

class RouteBindingProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        \Route::bind('mv', function(int $id): Post {
            return Post::current()->withoutGlobalScopes()->where('metadata->post_type', 'mv')->findOrFail($id);
        });
        \Route::bind('album', function(int $id): Post {
            return Post::current()->withoutGlobalScopes()->whereIn('metadata->post_type', ['album', 'single'])->findOrFail($id);
        });
    }
}

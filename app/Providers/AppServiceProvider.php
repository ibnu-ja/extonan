<?php

namespace App\Providers;

use App\Models\BasePost;
use App\Policies\PostPolicy;
use Gate;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Image;
use Plank\Mediable\Facades\ImageManipulator;
use Plank\Mediable\ImageManipulation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(BasePost::class, PostPolicy::class);

        ImageManipulator::defineVariant(
            'medium',
            ImageManipulation::make(function (Image $image) {
                $image->scaleDown(width: 300);
            })
        );

        ImageManipulator::defineVariant(
            'large',
            ImageManipulation::make(function (Image $image) {
                $image->scaleDown(width: 600);
            })
        );
    }
}

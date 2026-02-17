<?php

namespace App\Providers;

use App\Models\BasePost;
use App\Policies\PostPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );

        Gate::policy(BasePost::class, PostPolicy::class);
        //Gate::policy(Anime::class, PostPolicy::class);
        //Gate::policy(Post::class, PostPolicy::class);

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

<?php

namespace App\Http\Middleware;

use App\Services\Git;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'appVersion' => Cache::remember('git-latest-tag', 3600, fn () => app(Git::class)->getLatestTag()),
            'appCommitHash' => Cache::remember('git-commit-hash', 3600, fn () => app(Git::class)->getAppCommitHash()),
            'appBranch' => Cache::remember('git-branch', 3600, fn () => app(Git::class)->getAppBranch()),
            'appGitOriginRepo' => Cache::remember('git-origin-repo', 3600, fn () => app(Git::class)->getRepoUrl()),
        ];
    }
}

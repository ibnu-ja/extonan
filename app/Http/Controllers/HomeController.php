<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Show homepage
     */
    public function __invoke(): \Inertia\Response
    {
        return Inertia::render('Home/Index', [
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'latestAnime' => Anime::with('author')->take(5)->orderBy('published_at')->get(),
            'latestEpisodes' => Post::whereHasMorph('postable', [Anime::class], function (Builder $query) {
                $query->with('author')->take(10);
            })->with(['postable', 'author'])->orderByDesc('published_at')->get(),
        ]);
    }
}

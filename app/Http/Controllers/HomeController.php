<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Post;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Show homepage
     */
    public function __invoke(): \Inertia\Response
    {
        return Inertia::render('Home/Index', [
            'laravelVersion' => app()->version(),
            'phpVersion' => PHP_VERSION,
            'latestAnime' => Anime::with('author')->take(5)->orderBy('published_at')->get(),
            'latestEpisodes' => Post::whereHasMorph('postable', [Anime::class], function (Builder $query) {
                $query->with('author')->take(10);
            })->with(['postable', 'author'])->orderByDesc('published_at')->get(),
            'latestMv' => Post::with('author')
                ->where('metadata->post_type', '=', 'mv')
                ->current()
                ->take(10)->orderBy('published_at')->get(),
            'latestAlbum' => Post::with('author')
                ->whereIn('metadata->post_type', ['album', 'single'])
                ->current()
                ->take(10)->orderBy('published_at')->get(),
        ]);
    }
}

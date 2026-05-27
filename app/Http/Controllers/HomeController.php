<?php

namespace App\Http\Controllers;

use App\Data\AnimeSummaryData;
use App\Data\EpisodeSummaryData;
use App\Data\Home\HomePageResponse;
use App\Data\MusicSummaryData;
use App\Models\Anime;
use App\Models\Post;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\DataCollection;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $user = Auth::user();

        $latestAnime = Anime::visible()
            ->take(5)
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (Anime $a) => AnimeSummaryData::fromModel($a, $user));

        $latestEpisodes = Post::whereHasMorph('postable', [Anime::class], function (Builder $query) {
            $query->current();
        })->with(['postable', 'author'])
            ->take(15)
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (Post $p) => EpisodeSummaryData::fromModel($p, $user));

        $latestMv = Post::with('author')
            ->where('metadata->post_type', 'mv')
            ->current()
            ->take(10)
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (Post $p) => MusicSummaryData::fromModel($p, $user));

        $latestAlbum = Post::with('author')
            ->whereIn('metadata->post_type', ['album', 'single'])
            ->current()
            ->take(10)
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (Post $p) => MusicSummaryData::fromModel($p, $user));

        return Inertia::render('Home', new HomePageResponse(
            latestAnime: new DataCollection(AnimeSummaryData::class, $latestAnime),
            latestEpisodes: new DataCollection(EpisodeSummaryData::class, $latestEpisodes),
            latestMv: new DataCollection(MusicSummaryData::class, $latestMv),
            latestAlbum: new DataCollection(MusicSummaryData::class, $latestAlbum),
        ));
    }
}

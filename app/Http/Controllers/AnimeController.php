<?php

namespace App\Http\Controllers;

use App\Data\Anime\AnimeAZResponse;
use App\Data\Anime\AnimeFormData;
use App\Data\Anime\AnimeIndexResponse;
use App\Data\Anime\AnimeListItemData;
use App\Data\Anime\AnimeShowResponse;
use App\Data\EpisodeSummaryData;
use App\Data\LabelValue;
use App\Data\PaginationData;
use App\Http\Requests\StoreAnimeRequest;
use App\Models\Anime;
use App\Models\Post;
use App\Queries\AnimeSeasonsQuery;
use Gate;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Oddvalue\LaravelDrafts\Http\Middleware\WithDraftsMiddleware;
use Spatie\LaravelData\DataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AnimeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['index', 'show']),
            WithDraftsMiddleware::class,
        ];
    }

    public function index(Request $request): Application|RedirectResponse|Redirector|Response
    {
        if (! $request->has('sort')) {
            $query = $request->query();
            $query['sort'] = 'title->romaji';

            $redirectUrl = $request->url().'?'.http_build_query($query);

            return redirect($redirectUrl);
        }

        $validGenres = config('anime.genres', []);
        $validTags = config('anime.tags', []);

        $request->validate([
            'filter.genre_in.*' => ['nullable', 'string', 'in:'.implode(',', $validGenres)],
            'filter.genre_not_in.*' => ['nullable', 'string', 'in:'.implode(',', $validGenres)],
            'filter.tag_in.*' => ['nullable', 'string', 'in:'.implode(',', $validTags)],
            'filter.tag_not_in.*' => ['nullable', 'string', 'in:'.implode(',', $validTags)],
            'filter.season_in.*' => ['nullable', 'string'],
            'filter.season_not_in.*' => ['nullable', 'string'],
            'filter.title' => ['nullable', 'string', 'max:255'],
            'filter.is_published' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'string', 'in:title->romaji,-title->romaji,created_at,-created_at,updated_at,-updated_at'],
        ]);

        $user = Auth::user();

        $paginator = QueryBuilder::for(Anime::visible())->allowedFilters(
            AllowedFilter::scope('season_in'),
            AllowedFilter::scope('season_not_in'),
            AllowedFilter::scope('tag_in'),
            AllowedFilter::scope('tag_not_in'),
            AllowedFilter::scope('genre_in'),
            AllowedFilter::scope('genre_not_in'),
            AllowedFilter::scope('title', 'searchTitle'),
            AllowedFilter::exact('is_published'),
        )
            ->allowedSorts('title->romaji', 'created_at', 'updated_at')
            ->paginate(14)->appends(request()->query());

        $items = collect($paginator->items())
            ->map(fn (Anime $a) => AnimeListItemData::fromModel($a, $user));

        return Inertia::render('anime/Index', new AnimeIndexResponse(
            items: new DataCollection(AnimeListItemData::class, $items),
            pagination: new PaginationData(
                currentPage: $paginator->currentPage(),
                lastPage: $paginator->lastPage(),
                perPage: $paginator->perPage(),
                total: $paginator->total(),
                links: $paginator->linkCollection()->toArray(),
            ),
            seasons: (new AnimeSeasonsQuery)->builder()->get()->pluck('season_year')->toArray(),
            genres: new DataCollection(LabelValue::class, collect(config('anime.genres', []))->map(fn (string $genre) => new LabelValue(
                key: $genre,
                value: __('anime.genres.'.$genre),
            ))),
            tags: new DataCollection(LabelValue::class, collect(config('anime.tags', []))->map(fn (string $tag) => new LabelValue(
                key: $tag,
                value: __('anime.tags.'.$tag),
            ))),
            sortOptions: new DataCollection(LabelValue::class, collect(['title->romaji', '-title->romaji', 'created_at', '-created_at', 'updated_at', '-updated_at'])->map(function (string $sort) {
                $dir = str_starts_with($sort, '-') ? 'desc' : 'asc';
                $field = ltrim($sort, '-');

                return new LabelValue(
                    key: $sort,
                    value: __('anime.sort.'.$field).' '.__('anime.sort_dir.'.$dir),
                );
            })),
        ));
    }

    public function create(Request $request): Response
    {
        Gate::authorize('create', Anime::class);

        return Inertia::render('anime/Create', new AnimeFormData(
            id: null,
            title: [],
            description: [],
            anilistId: null,
            metadata: null,
            isPublished: false,
            canPublish: $request->user()->can('publish', Anime::class),
        ));
    }

    public function store(StoreAnimeRequest $request)
    {
        Gate::authorize('create', Anime::class);
        if ($request->boolean('is_published') && $request->user()->cannot('publish', Anime::class)) {
            abort(403);
        }

        Anime::create($request->validated());

        return redirect()->route('anime.index')->banner('Anime '.($request->boolean('is_published') ? 'published' : 'draft saved').' successfully.');
    }

    public function show(Anime $anime)
    {
        Gate::authorize('view', $anime);

        $user = Auth::user();
        $anime->load([
            'posts' => fn (MorphMany $query) => $query->orderByEpisodeAndNativeTitle()->visible()->with(['author'])->get(),
            'author',
            'publisher',
        ]);

        return Inertia::render('anime/Show', new AnimeShowResponse(
            anime: AnimeListItemData::fromModel($anime, $user),
            episodes: new DataCollection(EpisodeSummaryData::class, $anime->posts->map(
                fn ($post) => EpisodeSummaryData::fromModel($post, $user)
            )),
            canCreateEpisode: auth()->check() && auth()->user()?->can('create', Post::class),
        ));
    }

    public function edit(Anime $anime)
    {
        Gate::authorize('update', $anime);
        if (auth()->user()->cannot('update', $anime)) {
            abort(403);
        }

        return Inertia::render('anime/Create', [
            'anime' => AnimeFormData::fromModel($anime, auth()->user()->can('publish', $anime)),
        ]);
    }

    public function update(StoreAnimeRequest $request, Anime $anime)
    {
        Gate::authorize('update', $anime);
        if ($request->boolean('is_published') && $request->user()->cannot('publish', Anime::class)) {
            abort(403);
        }

        $anime->update($request->validated());

        return redirect()->route('anime.show', $anime)->banner('Anime updated successfully.');
    }

    public function destroy(Anime $anime)
    {
        Gate::authorize('delete', $anime);
        $anime->delete();

        return redirect()->route('anime.index')->banner('Anime successfully deleted.');
    }

    public function az(): Response
    {
        $user = Auth::user();

        $anime = Anime::visible()
            ->orderBy('title->romaji')
            ->get()
            ->map(fn (Anime $a) => AnimeListItemData::fromModel($a, $user));

        return Inertia::render('anime/AZ', new AnimeAZResponse(
            items: new DataCollection(AnimeListItemData::class, $anime),
            canCreate: auth()->check() && auth()->user()?->can('create', Post::class),
        ));
    }
}

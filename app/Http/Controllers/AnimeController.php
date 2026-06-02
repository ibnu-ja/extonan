<?php

namespace App\Http\Controllers;

use App\Data\Anime\AnimeAZResponse;
use App\Data\Anime\AnimeCreateResponse;
use App\Data\Anime\AnimeFormData;
use App\Data\Anime\AnimeIndexRequest;
use App\Data\Anime\AnimeIndexResponse;
use App\Data\Anime\AnimeListItemData;
use App\Data\Anime\AnimeShowResponse;
use App\Data\Anime\AnimeStoreData;
use App\Data\EpisodeSummaryData;
use App\Data\LabelValue;
use App\Data\PaginatedCollection;
use App\Data\TagItem;
use App\Enums\Season;
use App\Models\Anime;
use App\Models\Post;
use App\Queries\AnimeSeasonsQuery;
use App\Services\AnilistService;
use Gate;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    /**
     * @see AnimeIndexResponse
     */
    public function index(Request $request): RedirectResponse|Redirector|Response
    {
        if (! $request->has('sort')) {
            $query = $request->query();
            $query['sort'] = 'title->romaji';

            $redirectUrl = $request->url().'?'.http_build_query($query);

            return redirect($redirectUrl);
        }

        $data = AnimeIndexRequest::from($request);

        $request->merge(['perPage' => $data->perPage]);

        $user = Auth::user();

        $paginator = QueryBuilder::for(Anime::visible())->allowedFilters(
            AllowedFilter::scope('seasonIn'),
            AllowedFilter::scope('seasonNotIn'),
            AllowedFilter::scope('tagIn'),
            AllowedFilter::scope('tagNotIn'),
            AllowedFilter::scope('genreIn'),
            AllowedFilter::scope('genreNotIn'),
            AllowedFilter::scope('title', 'searchTitle'),
            AllowedFilter::exact('isPublished', 'is_published'),
        )
            ->allowedSorts('title->romaji', 'created_at', 'updated_at')
            ->select([
                'id', 'title', 'slug', 'author_id', 'metadata',
                'created_at', 'updated_at',
                'is_published', 'is_current', 'uuid', 'published_at',
                'publisher_type', 'publisher_id',
            ])
            ->paginate($data->perPage)->appends($request->except(['page']));

        return Inertia::render('anime/Index', [
            'anime' => PaginatedCollection::fromPaginator(
                $paginator,
                fn (Anime $a) => AnimeListItemData::fromModel($a, $user),
            ),
            'seasons' => Inertia::once(fn () => (new AnimeSeasonsQuery)->builder()->get()->pluck('season_year')->toArray()),
            'genres' => Inertia::once(fn () => $this->getGenres()),
            'tags' => Inertia::once(fn () => $this->getTags()),
            'sortOptions' => Inertia::once(fn () => $this->getSortOptions()),
            'perPageValues' => Inertia::once(fn () => AnimeIndexRequest::PER_PAGE_VALUES),
        ]);
    }

    private function getGenres(): DataCollection
    {
        return new DataCollection(LabelValue::class, collect(config('anime.genres', []))->map(fn (string $genre) => new LabelValue(
            key: $genre,
            value: __('anime.genres.'.$genre),
        )));
    }

    private function getTags(): DataCollection
    {
        $tags = Cache::remember('anilist-tags', 86400, function () {
            $path = storage_path('app/anilist-tags.json');

            if (file_exists($path)) {
                return json_decode(file_get_contents($path), true);
            }

            return collect(config('anime.tags', []))->map(fn (string $name) => [
                'id' => 0,
                'name' => $name,
                'isAdult' => false,
            ])->all();
        });

        return new DataCollection(TagItem::class, array_map(fn (array $t) => new TagItem(
            id: $t['id'],
            name: $t['name'],
            isAdult: $t['isAdult'] ?? false,
        ), $tags));
    }

    private function getSortOptions(): DataCollection
    {
        return new DataCollection(LabelValue::class, collect(['title->romaji', '-title->romaji', 'created_at', '-created_at', 'updated_at', '-updated_at'])->map(function (string $sort) {
            $dir = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $field = ltrim($sort, '-');

            return new LabelValue(
                key: $sort,
                value: __('anime.sort.'.$field).' '.__('anime.sort_dir.'.$dir),
            );
        }));
    }

    /**
     * @see AnimeCreateResponse
     */
    public function create(Request $request): Response
    {
        Gate::authorize('create', Anime::class);

        return Inertia::render('anime/Create', [
            'anime' => null,
            'genres' => Inertia::once(fn () => $this->getGenres()),
            'tags' => Inertia::once(fn () => $this->getTags()),
            'seasons' => Inertia::once(fn () => Season::cases()),
            'anilistQuery' => Inertia::once(fn () => app(AnilistService::class)->getAnimeQuery()),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Anime::class);

        $data = AnimeStoreData::from($request);

        if ($data->isPublished && $request->user()->cannot('publish', Anime::class)) {
            abort(403);
        }

        Anime::create($data->toModelArray());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Anime '.($data->isPublished ? 'published' : 'draft saved').' successfully.']);

        return redirect()->route('anime.index');
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

    /**
     * @see AnimeCreateResponse
     */
    public function edit(Anime $anime)
    {
        Gate::authorize('update', $anime);

        return Inertia::render('anime/Create', [
            'anime' => AnimeFormData::fromModel($anime, auth()->user()->can('publish', $anime)),
            'genres' => Inertia::once(fn () => $this->getGenres()),
            'tags' => Inertia::once(fn () => $this->getTags()),
            'seasons' => Inertia::once(fn () => Season::cases()),
            'anilistQuery' => Inertia::once(fn () => app(AnilistService::class)->getAnimeQuery()),
        ]);
    }

    public function update(Request $request, Anime $anime)
    {
        Gate::authorize('update', $anime);

        $data = AnimeStoreData::from($request);

        if ($data->isPublished && $request->user()->cannot('publish', Anime::class)) {
            abort(403);
        }

        $anime->update($data->toModelArray());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Anime updated successfully.']);

        return redirect()->route('anime.show', $anime);
    }

    public function destroy(Anime $anime)
    {
        Gate::authorize('delete', $anime);
        $anime->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Anime successfully deleted.']);

        return redirect()->route('anime.index');
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

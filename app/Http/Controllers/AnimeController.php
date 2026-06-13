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
use App\Data\Anime\EpisodeShowData;
use App\Data\LabelValue;
use App\Data\TagItem;
use App\Enums\Season;
use App\Models\Anime;
use App\Models\Post;
use App\Models\User;
use App\Queries\AnimeSeasonsQuery;
use App\Services\AnilistService;
use App\Services\AnimeService;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Oddvalue\LaravelDrafts\Http\Middleware\WithDraftsMiddleware;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AnimeController extends Controller implements HasMiddleware
{
    public function __construct(private readonly AnimeService $animeService) {}

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['index', 'show']),
            WithDraftsMiddleware::class,
        ];
    }

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

        $items = $paginator->getCollection()->map(fn (Anime $a) => AnimeListItemData::fromModel($a, $user));
        $paginator->setCollection($items);

        return Inertia::render('anime/Index', new AnimeIndexResponse(
            anime: new PaginatedDataCollection(AnimeListItemData::class, $paginator),
            seasons: (new AnimeSeasonsQuery)->builder()->get()->pluck('season_year')->toArray(),
            genres: new DataCollection(LabelValue::class, $this->animeService->getGenres()),
            tags: new DataCollection(TagItem::class, $this->animeService->getTags()),
            sortOptions: new DataCollection(LabelValue::class, $this->animeService->getSortOptions()),
            perPageValues: AnimeIndexRequest::PER_PAGE_VALUES,
        ));
    }

    public function create(Request $request): Response
    {
        Gate::authorize('create', Anime::class);

        return Inertia::render('anime/Create', new AnimeCreateResponse(
            genres: new DataCollection(LabelValue::class, $this->animeService->getGenres()),
            tags: new DataCollection(TagItem::class, $this->animeService->getTags()),
            seasons: Season::cases(),
            anilistQuery: app(AnilistService::class)->getAnimeQuery(),
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Anime::class);

        $data = AnimeStoreData::from($request);

        $this->authorizePublishIfRequested($data, $request->user());

        Anime::create($data->toModelArray());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Anime '.($data->isPublished ? 'published' : 'draft saved').' successfully.']);

        return redirect()->route('anime.index');
    }

    public function show(Anime $anime): Response
    {
        Gate::authorize('view', $anime);

        $user = Auth::user();
        $anime->load([
            'posts' => fn (MorphMany $query) => $query->orderByEpisodeAndNativeTitle()->visible()->with(['author']),
            'author',
            'publisher',
        ]);

        return Inertia::render('anime/Show', new AnimeShowResponse(
            anime: AnimeListItemData::fromModel($anime, $user),
            episodes: new DataCollection(EpisodeShowData::class, $anime->posts->map(
                fn ($post) => EpisodeShowData::fromModel($post, $anime, $user)
            )),
            canCreateEpisode: auth()->check() && auth()->user()?->can('create', Post::class),
            metadata: $anime->metadata,
        ));
    }

    public function edit(Anime $anime): Response
    {
        Gate::authorize('update', $anime);

        return Inertia::render('anime/Create', new AnimeCreateResponse(
            genres: new DataCollection(LabelValue::class, $this->animeService->getGenres()),
            tags: new DataCollection(TagItem::class, $this->animeService->getTags()),
            seasons: Season::cases(),
            anilistQuery: app(AnilistService::class)->getAnimeQuery(),
            anime: AnimeFormData::fromModel($anime, auth()->user()->can('publish', $anime)),
        ));
    }

    public function update(Request $request, Anime $anime): RedirectResponse
    {
        Gate::authorize('update', $anime);

        $data = AnimeStoreData::from($request);

        $this->authorizePublishIfRequested($data, $request->user(), $anime);

        $anime->update($data->toModelArray());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Anime updated successfully.']);

        return redirect()->route('anime.show', $anime);
    }

    public function destroy(Anime $anime): RedirectResponse
    {
        Gate::authorize('delete', $anime);
        $anime->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Anime successfully deleted.']);

        return redirect()->route('anime.index');
    }

    private function authorizePublishIfRequested(AnimeStoreData $data, User $user, ?Anime $anime = null): void
    {
        $requestingPublish = $anime
            ? $data->isPublished && ! $anime->is_published
            : $data->isPublished;

        if ($requestingPublish && $user->cannot('publish', Anime::class)) {
            abort(403);
        }
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

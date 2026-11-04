<?php

namespace App\Http\Controllers;

use App\Data\Anime\PostShowData;
use App\Data\Music\AlbumIndexResponse;
use App\Data\Music\MusicShowResponse;
use App\Data\MusicSummaryData;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Oddvalue\LaravelDrafts\Http\Middleware\WithDraftsMiddleware;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AlbumController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['index', 'show']),
            WithDraftsMiddleware::class,
        ];
    }

    public function index(Request $request): Response
    {
        $perPage = $request->integer('perPage', 15);
        $user = Auth::user();

        $query = QueryBuilder::for(
            Post::whereIn('metadata->post_type', ['album', 'single'])
                ->with(['author', 'media.originalMedia.variants', 'media.variants'])
                ->visible()
        )->allowedFilters(
            AllowedFilter::callback('type', function ($q, $value) {
                if (in_array($value, ['album', 'single'], true)) {
                    $q->where('metadata->post_type', $value);
                }
            }),
            AllowedFilter::scope('title', 'searchTitle'),
        );

        if ($perPage === -1) {
            $results = $query->get();
            $paginator = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $paginator = $query->paginate($perPage);
        }
        $paginator->appends($request->query());

        $items = $paginator->getCollection()->map(fn (Post $p) => MusicSummaryData::fromModel($p, $user));
        $paginator->setCollection($items);

        return Inertia::render('album/Index', new AlbumIndexResponse(
            albums: new PaginatedDataCollection(MusicSummaryData::class, $paginator),
            canCreate: $user?->can('create', Post::class) ?? false,
            canViewUnpublished: $user?->can('viewAny', Post::class) ?? false,
        ));
    }

    public function show(Request $request, Post $album): Response
    {
        Gate::authorize('view', $album);

        $user = Auth::user();
        $album->load(['author', 'links', 'media.originalMedia.variants', 'media.variants']);

        // Recommendation pipeline for Album sidebar (target: 8 items)
        $limit = 8;
        $candidates = collect();

        // 1. Search candidates via Scout BM25 (artist + title)
        $artist = data_get($album->metadata, 'artist')
            ?? data_get($album->metadata, 'vgmdb_data.performers.0.names.en')
            ?? data_get($album->metadata, 'vgmdb_data.performers.0.names.ja')
            ?? data_get($album->metadata, 'vgmdb_data.vocals.0.names.en')
            ?? data_get($album->metadata, 'vgmdb_data.vocals.0.names.ja');
        $titleRomaji = $album->getTranslation('title', 'romaji');
        $titleEn = $album->getTranslation('title', 'en');
        $titleId = $album->getTranslation('title', 'id');
        $searchTerms = trim(($artist ? $artist.' ' : '').($titleRomaji ?: $titleEn ?: $titleId ?: ''));

        if (! empty($searchTerms)) {
            try {
                $candidates = Post::search($searchTerms)
                    ->query(fn ($q) => $q->whereIn('metadata->post_type', ['album', 'single'])
                        ->where('id', '!=', $album->id)
                        ->visible()
                    )
                    ->take($limit)
                    ->get();
            } catch (\Throwable) {
                $candidates = collect();
            }
        }

        // 2. Backfill with latest visible albums/singles if fewer than target
        if ($candidates->count() < $limit) {
            $needed = $limit - $candidates->count();
            $excludeIds = array_merge([$album->id], $candidates->pluck('id')->all());

            $backfill = Post::whereIn('metadata->post_type', ['album', 'single'])
                ->whereNotIn('id', $excludeIds)
                ->visible()
                ->latest('published_at')
                ->take($needed)
                ->get();

            $candidates = $candidates->merge($backfill);
        }

        $candidates->loadMissing(['author', 'media.originalMedia.variants', 'media.variants']);
        $related = MusicSummaryData::collect(
            $candidates->map(fn (Post $p) => MusicSummaryData::fromModel($p, $user)),
            DataCollection::class
        );

        return Inertia::render('album/Show', new MusicShowResponse(
            post: PostShowData::fromModel($album, $user),
            related: $related,
            canPublish: $user?->can('publish', $album) ?? false,
        ));
    }

    public function create(Request $request): RedirectResponse
    {
        $type = $request->query('type', 'album');

        return redirect()->route('shinrai.create', [
            'type' => in_array($type, ['album', 'single'], true) ? $type : 'album',
        ]);
    }

    public function edit(Request $request, Post $album): RedirectResponse
    {
        Gate::authorize('update', $album);

        return redirect()->route('shinrai.edit', $album);
    }
}

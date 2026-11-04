<?php

namespace App\Http\Controllers;

use App\Data\Anime\PostShowData;
use App\Data\Music\MusicIndexResponse;
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

class MVController extends Controller implements HasMiddleware
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

        $query = Post::where('metadata->post_type', 'mv')
            ->with(['author', 'media.originalMedia.variants', 'media.variants'])
            ->visible();

        if ($perPage === -1) {
            $results = $query->get();
            $paginator = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $paginator = $query->paginate($perPage);
        }
        $paginator->appends($request->query());

        $items = $paginator->getCollection()->map(fn (Post $p) => MusicSummaryData::fromModel($p, $user));
        $paginator->setCollection($items);

        return Inertia::render('mv/Index', new MusicIndexResponse(
            mv: new PaginatedDataCollection(MusicSummaryData::class, $paginator),
            canCreate: $user?->can('create', Post::class) ?? false,
            canViewUnpublished: $user?->can('viewAny', Post::class) ?? false,
        ));
    }

    public function create(Request $request): RedirectResponse
    {
        return redirect()->route('shinrai.create', [
            'type' => 'mv',
        ]);
    }

    public function show(Request $request, Post $mv): Response
    {
        Gate::authorize('view', $mv);

        $user = Auth::user();
        $mv->load(['author', 'links', 'embed', 'saluran', 'media.originalMedia.variants', 'media.variants']);

        // Recommendation pipeline for MV sidebar (target: 8 items)
        $limit = 8;
        $candidates = collect();

        // 1. Search candidates via Scout BM25 (artist + title)
        $artist = data_get($mv->metadata, 'artist') ?? data_get($mv->metadata, 'vgmdb_data.performers.0.names.en') ?? data_get($mv->metadata, 'vgmdb_data.performers.0.names.ja');
        $titleRomaji = $mv->getTranslation('title', 'romaji');
        $titleEn = $mv->getTranslation('title', 'en');
        $titleId = $mv->getTranslation('title', 'id');
        $searchTerms = trim(($artist ? $artist.' ' : '').($titleRomaji ?: $titleEn ?: $titleId ?: ''));

        if (! empty($searchTerms)) {
            try {
                $candidates = Post::search($searchTerms)
                    ->query(fn ($q) => $q->where('metadata->post_type', 'mv')
                        ->where('id', '!=', $mv->id)
                        ->visible()
                    )
                    ->take($limit)
                    ->get();
            } catch (\Throwable) {
                $candidates = collect();
            }
        }

        // 2. Backfill with latest visible MVs if fewer than target
        if ($candidates->count() < $limit) {
            $needed = $limit - $candidates->count();
            $excludeIds = array_merge([$mv->id], $candidates->pluck('id')->all());

            $backfill = Post::where('metadata->post_type', 'mv')
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

        return Inertia::render('mv/Show', new MusicShowResponse(
            post: PostShowData::fromModel($mv, $user),
            related: $related,
            canPublish: $user?->can('publish', $mv) ?? false,
        ));
    }

    public function edit(Request $request, Post $mv): RedirectResponse
    {
        Gate::authorize('update', $mv);

        return redirect()->route('shinrai.edit', $mv);
    }
}

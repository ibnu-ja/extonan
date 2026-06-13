<?php

namespace App\Http\Controllers;

use App\Data\Anilist\AnilistMediaData;
use App\Data\Anime\AnimeListItemData;
use App\Data\Anime\EpisodeListItemData;
use App\Data\Anime\PostCreateResponse;
use App\Data\Anime\PostFormData;
use App\Data\Anime\PostShowData;
use App\Data\Anime\PostShowResponse;
use App\Data\Anime\PostStoreData;
use App\Models\Anime;
use App\Models\Post;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Oddvalue\LaravelDrafts\Http\Middleware\WithDraftsMiddleware;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['show']),
            WithDraftsMiddleware::class,
        ];
    }

    /**
     * @see PostCreateResponse
     */
    public function create(Anime $anime): Response
    {
        Gate::authorize('create', Post::class);

        return Inertia::render('anime/post/Create', new PostCreateResponse(
            anime: AnimeListItemData::fromModel($anime, Auth::user()),
            canPublish: Auth::user()?->can('publish', Post::class) ?? false,
            metadata: $anime->metadata ? AnilistMediaData::from($anime->metadata) : null,
        ));
    }

    public function store(Request $request, Anime $anime): RedirectResponse
    {
        Gate::authorize('create', Post::class);

        $data = PostStoreData::from($request);

        $this->authorizePublishIfRequested($data);

        $post = $anime->posts()->create($data->toModelArray());

        if ($data->links !== null) {
            $post->resources()->createMany($data->links);
        }

        if ($data->thumbnailItem !== null) {
            $post->syncMedia($data->thumbnailItem['id'], 'thumbnail');
        } else {
            $post->detachMediaTags('thumbnail');
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Episode created successfully!']);

        return redirect()->route('post.show', [$anime, $post]);
    }

    /**
     * @see PostShowResponse
     */
    public function show(Anime $anime, Post $post): Response
    {
        Gate::authorize('view', $anime);
        Gate::authorize('view', $post);

        $user = Auth::user();

        $anime->load(['posts' => fn (MorphMany $query) => $query->current()->with(['media.originalMedia.variants', 'media.variants'])->orderByEpisodeAndNativeTitle()]);
        $post->load(['author', 'links', 'saluran', 'embeds', 'media.originalMedia.variants', 'media.variants']);

        return Inertia::render('anime/post/Show', [
            'anime' => AnimeListItemData::fromModel($anime, $user),
            'episodes' => $anime->posts->map(fn (Post $p) => EpisodeListItemData::fromModel($p)),
            'post' => PostShowData::fromModel($post, $user),
        ]);
    }

    /**
     * @see PostCreateResponse
     */
    public function edit(Anime $anime, Post $post): Response
    {
        Gate::authorize('update', $post);

        $user = Auth::user();

        return Inertia::render('anime/post/Create', new PostCreateResponse(
            anime: AnimeListItemData::fromModel($anime, $user),
            post: PostFormData::fromModel($post, $user?->can('publish', $post) ?? false),
            canPublish: $user?->can('publish', $post) ?? false,
            metadata: $anime->metadata ? AnilistMediaData::from($anime->metadata) : null,
        ));
    }

    public function update(Request $request, Anime $anime, Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        $data = PostStoreData::from($request);

        $this->authorizePublishIfRequested($data);

        $post->update($data->toModelArray());

        if ($data->links !== null) {
            $links = collect($data->links)->map(fn ($item) => array_filter([
                'id' => $item['id'] ?? null,
                'name' => $item['name'],
                'type' => $item['type'],
                'value' => json_encode($item['value']),
            ], fn ($value, $key) => ! (is_null($value) && $key === 'id'), ARRAY_FILTER_USE_BOTH));

            $post->links()->upsert($links->toArray(), uniqueBy: ['id'], update: ['name', 'value']);
        }

        if ($data->thumbnailItem !== null) {
            $post->syncMedia($data->thumbnailItem['id'], 'thumbnail');
        } else {
            $post->detachMediaTags('thumbnail');
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Episode updated successfully!']);

        return redirect()->back();
    }

    public function destroy(Anime $anime, Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Episode deleted successfully!']);

        return redirect()->route('anime.show', $anime);
    }

    private function authorizePublishIfRequested(PostStoreData $data): void
    {
        if ($data->isPublished && Auth::user()?->cannot('publish', Post::class)) {
            abort(403);
        }
    }
}

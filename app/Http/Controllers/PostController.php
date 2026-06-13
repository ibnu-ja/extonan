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
use App\Data\Anime\ResourceData;
use App\Enums\ResourceType;
use App\Models\Anime;
use App\Models\Post;
use App\Models\User;
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
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['show']),
            WithDraftsMiddleware::class,
        ];
    }

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

        $this->authorizePublishIfRequested($data, $request->user());

        $post = $anime->posts()->create($data->toModelArray());

        $post->resources()->createMany(array_map(fn (ResourceData $r) => $r->toArray(), $data->links));

        if ($data->embed !== null) {
            $post->resources()->create($data->embed->toArray());
        }

        if ($data->saluran !== null) {
            $post->resources()->create($data->saluran->toArray());
        }

        if ($data->thumbnailItem !== null) {
            $post->syncMedia($data->thumbnailItem->id, 'thumbnail');
        } else {
            $post->detachMediaTags('thumbnail');
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Episode created successfully!']);

        return redirect()->route('post.show', [$anime, $post]);
    }

    public function show(Anime $anime, Post $post): Response
    {
        Gate::authorize('view', $anime);
        Gate::authorize('view', $post);

        $user = Auth::user();

        $anime->load(['posts' => fn (MorphMany $query) => $query->current()->with(['media.originalMedia.variants', 'media.variants'])->orderByEpisodeAndNativeTitle()]);
        $post->load(['author', 'links', 'saluran', 'embed', 'media.originalMedia.variants', 'media.variants']);

        $episodes = new DataCollection(EpisodeListItemData::class, $anime->posts->map(
            fn (Post $p) => EpisodeListItemData::fromModel($p),
        ));

        return Inertia::render('anime/post/Show', new PostShowResponse(
            anime: AnimeListItemData::fromModel($anime, $user),
            episodes: $episodes,
            post: PostShowData::fromModel($post, $user),
        ));
    }

    public function edit(Anime $anime, Post $post): Response
    {
        Gate::authorize('update', $post);

        $user = Auth::user();
        $post->load(['links', 'embed', 'saluran']);

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
        $this->authorizePublishIfRequested($data, $request->user(), $post);
        $post->update($data->toModelArray());
        $this->upsertResources($post, ResourceType::Link, $data->links);
        $this->replaceSingleResource($post, ResourceType::Embed, $data->embed);
        $this->replaceSingleResource($post, ResourceType::Saluran, $data->saluran);

        if ($data->thumbnailItem !== null) {
            $post->syncMedia($data->thumbnailItem->id, 'thumbnail');
        } else {
            $post->detachMediaTags('thumbnail');
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Episode updated successfully!']);

        return redirect()->route('post.show', [$anime, $post]);
    }

    public function destroy(Anime $anime, Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Episode deleted successfully!']);

        return redirect()->route('anime.show', $anime);
    }

    /** @param ResourceData[] $items */
    private function upsertResources(Post $post, ResourceType $type, array $items): void
    {
        $submittedIds = collect($items)
            ->map(fn (ResourceData $r) => $r->id instanceof Optional ? null : $r->id)
            ->filter()
            ->values();

        $query = $post->resources()->where('type', $type);

        if ($submittedIds->isNotEmpty()) {
            $query->whereNotIn('id', $submittedIds);
        }

        $query->delete();

        if ($items === []) {
            return;
        }

        $post->resources()->upsert(
            array_map(fn (ResourceData $r) => [...$r->toArray(), 'value' => json_encode($r->value)], $items),
            uniqueBy: ['id'],
            update: ['name', 'type', 'value'],
        );
    }

    private function replaceSingleResource(Post $post, ResourceType $type, ?ResourceData $item): void
    {
        $post->resources()->where('type', $type)->delete();

        if ($item !== null) {
            $post->resources()->create($item->toArray());
        }
    }

    private function authorizePublishIfRequested(PostStoreData $data, ?User $user, ?Post $post = null): void
    {
        $requestingPublish = $post
            ? $data->isPublished && ! $post->is_published
            : $data->isPublished;

        if ($requestingPublish && $user?->cannot('publish', Post::class)) {
            abort(403);
        }
    }
}

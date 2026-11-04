<?php

namespace App\Http\Controllers;

use App\Data\Anime\PostFormData;
use App\Data\Anime\ResourceData;
use App\Data\Music\ShinraiCreateResponse;
use App\Data\Music\ShinraiStoreData;
use App\Enums\ResourceType;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Oddvalue\LaravelDrafts\Http\Middleware\WithDraftsMiddleware;
use Spatie\LaravelData\Optional;

class ShinraiPostController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            WithDraftsMiddleware::class,
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        Gate::authorize('create', Post::class);

        $type = request()->query('type', 'mv');
        $user = Auth::user();

        return Inertia::render('shinrai/Create', new ShinraiCreateResponse(
            type: $type,
            canPublish: $user?->can('publish', Post::class) ?? false,
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Post::class);

        $data = ShinraiStoreData::from($request);
        $this->authorizePublishIfRequested($data, $request->user());

        $post = Post::create($data->toModelArray());

        if (! empty($data->links)) {
            $post->resources()->createMany(array_map(fn (ResourceData $r) => $r->toArray(), $data->links));
        }

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

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Post created successfully!']);

        $route = $data->postType === 'mv' ? 'mv.show' : 'album.show';

        return redirect()->route($route, $post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $shinrai): Response
    {
        Gate::authorize('update', $shinrai);

        $user = Auth::user();
        $shinrai->load(['links', 'embed', 'saluran', 'media.originalMedia.variants', 'media.variants']);

        return Inertia::render('shinrai/Create', new ShinraiCreateResponse(
            type: $shinrai->metadata?->post_type ?? 'mv',
            post: PostFormData::fromModel($shinrai, $user?->can('publish', $shinrai) ?? false),
            canPublish: $user?->can('publish', $shinrai) ?? false,
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $shinrai): RedirectResponse
    {
        Gate::authorize('update', $shinrai);

        $data = ShinraiStoreData::from($request);
        $this->authorizePublishIfRequested($data, $request->user(), $shinrai);

        $shinrai->update($data->toModelArray());

        $this->upsertResources($shinrai, ResourceType::Link, $data->links);
        $this->replaceSingleResource($shinrai, ResourceType::Embed, $data->embed);
        $this->replaceSingleResource($shinrai, ResourceType::Saluran, $data->saluran);

        if ($data->thumbnailItem !== null) {
            $shinrai->syncMedia($data->thumbnailItem->id, 'thumbnail');
        } else {
            $shinrai->detachMediaTags('thumbnail');
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Post updated successfully!']);

        $route = ($shinrai->metadata?->post_type ?? 'mv') === 'mv' ? 'mv.show' : 'album.show';

        return redirect()->route($route, $shinrai);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $shinrai): RedirectResponse
    {
        Gate::authorize('delete', $shinrai);

        $type = $shinrai->metadata?->post_type ?? 'mv';
        $shinrai->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Post deleted successfully!']);

        $route = $type === 'mv' ? 'mv.index' : 'album.index';

        return redirect()->route($route);
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

    private function authorizePublishIfRequested(ShinraiStoreData $data, ?User $user, ?Post $post = null): void
    {
        $requestingPublish = $post
            ? $data->isPublished && ! $post->is_published
            : $data->isPublished;

        if ($requestingPublish && $user?->cannot('publish', Post::class)) {
            abort(403);
        }
    }
}

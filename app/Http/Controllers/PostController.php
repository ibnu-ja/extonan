<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Anime;
use App\Models\Post;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Oddvalue\LaravelDrafts\Http\Middleware\WithDraftsMiddleware;

class PostController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['show', 'store']),
            WithDraftsMiddleware::class
        ];
    }
//    /**
//     * Display a listing of the resource.
//     */
//    public function index()
//    {
//        //
//    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Anime $anime): \Inertia\Response
    {
        \Gate::authorize('create', Post::class);

        return Inertia::render('Anime/Post/Create', [
            'anime' => $anime,
            'canPublish' => request()->user()->can('publish', Post::class),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Anime $anime, StorePostRequest $request)
    {
        // if user wants to publish but does not have capability to publish
        // or user cannot create
        \Gate::authorize('create', Post::class);
        if ($request->boolean('is_published') && $request->user()->cannot('publish', Post::class)) {
            abort(403);
        }
//        return $request->all();
        $post = $anime->posts()->create($request->validated());

        $post->resources()->createMany($request->validated()['resources']);

        if (!is_null($request->validated()['thumbnail_item'])) {
            $post->syncMedia($request->validated()['thumbnail_item']['id'], 'thumbnail');
        } else {
            $post->detachMediaTags('thumbnail');
        }

        return redirect()->route('post.show', [$anime, $post])->banner('Episode created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Anime $anime, Post $post)
    {
        \Gate::authorize('view', $anime);
        \Gate::authorize('view', $post);

        return Inertia::render('Anime/Post/Show', [
            'anime' => $anime->load(['posts' => fn(MorphMany $query) => $query->current()->orderByEpisodeAndNativeTitle('desc')]),
            'post' => $post->load([
                'author',
                'links' => fn(HasMany $query) => $query->orderBy('name'),
                'saluran' => fn(HasMany $query) => $query->orderBy('name'),
                'media'
            ]),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Anime $anime, Post $post)
    {
        \Gate::authorize('update', $post);

        return Inertia::render('Anime/Post/Create', [
            'anime' => $anime,
            'post' => $post->load(['author', 'resources', 'media'])->append('thumbnail_item'),
            'canPublish' => request()->user()->can('publish', $post),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Anime $anime, Post $post, StorePostRequest $request)
    {
        \Gate::authorize('update', $post);
        if ($request->boolean('is_published') && $request->user()->cannot('publish', Post::class)) {
            abort(403);
        }
        $validated = $request->validated();

        // return $validated['resources'];

        $resources = collect($validated['resources'])->map(function ($item) {
            return array_filter([
                'id' => $item['id'] ?? null,
                'name' => $item['name'],
                'type' => $item['type'],
                'value' => json_encode($item['value'])
            ], function ($value, $key) {
                return !(is_null($value) && $key === 'id');
            }, ARRAY_FILTER_USE_BOTH);
        });
        $post->update($validated);
        $resourcesWithId = $resources->filter(fn($r) => isset($r['id']))->values()->toArray();
        $resourcesWithoutId = $resources->filter(fn($r) => !isset($r['id']))->values()->map(function ($resource) {
            $resource['value'] = json_decode($resource['value'], true); // Decode JSON string to array
            return $resource;
        })->toArray();

        if (!empty($resourcesWithId)) {
            $post->resources()->upsert(
                $resourcesWithId,
                ['id'],
                ['name', 'value', 'type']
            );
        }
        if (!empty($resourcesWithoutId)) {
            $post->resources()->createMany($resourcesWithoutId);
        }
        if (!is_null($request->validated()['thumbnail_item'])) {
            $post->syncMedia($request->validated()['thumbnail_item']['id'], 'thumbnail');
        } else {
            $post->detachMediaTags('thumbnail');
        }

        return redirect()->refresh()->banner('Episode updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anime $anime, Post $post)
    {
        \Gate::authorize('delete', $post);
        $post->delete();

        return redirect()->route('anime.show', $anime)->banner('Episode deleted successfully!');
    }
}

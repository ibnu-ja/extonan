<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Anime;
use App\Models\Post;
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
        if (auth()->user()->cannot('create', Post::class)) {
            abort(403);
        }

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
        if ($request->boolean('is_published') && $request->user()->cannot('publish', Post::class) || $request->user()->cannot('create', Post::class)) {
            abort(403);
        }
//        return $request->all();
        $post = $anime->posts()->create($request->validated());

        $post->resources()->createMany($request->validated()['links']);

        if (!is_null($request->validated()['thumbnail'])) {
            $post->syncMedia($request->validated()['thumbnail']['id'], 'thumbnail');
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
        \Gate::authorize('view', $post);

        return Inertia::render('Anime/Post/Show', [
            'anime' => $anime->load(['posts' => fn(MorphMany $query) => $query->orderByDesc('title->native')]),
            'post' => $post->load(['author', 'links', 'media']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Anime $anime, Post $post)
    {
        if (auth()->user()->cannot('update', $post)) {
            abort(403);
        }
        return Inertia::render('Anime/Post/Create', [
            'anime' => $anime,
            'post' => $post->load(['author', 'links', 'media']),
            'canPublish' => request()->user()->can('publish', $post),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Anime $anime, Post $post, StorePostRequest $request)
    {
        if ($request->boolean('is_published') && $request->user()->cannot('publish', Post::class)) {
            abort(403);
        }
        $validated = $request->validated();

        $linksss = collect($validated['links'])->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'type' => $item['type'],
                'value' => json_encode($item['value'])
            ];
        });

        $post->update($validated);

        $post->links()->upsert($linksss->toArray(), uniqueBy: ['id'], update: ['name', 'value']);

        if (!is_null($request->validated()['thumbnail'])) {
            $post->syncMedia($request->validated()['thumbnail']['id'], 'thumbnail');
        } else {
            $post->detachMediaTags('thumbnail');
        }

        return redirect()->back()->banner('Episode updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anime $anime, Post $post)
    {
        if (auth()->user()->cannot('delete', $post)) {
            abort(403);
        }
        $post->delete();

        return redirect()->route('anime.show', $anime)->banner('Episode deleted successfully!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\StoreShinraiPostRequest;
use App\Models\Post;
use Illuminate\Routing\Controllers\HasMiddleware;
use Inertia\Inertia;
use Oddvalue\LaravelDrafts\Http\Middleware\WithDraftsMiddleware;

class ShinraiPostController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            //new Middleware('auth', except: ['show', 'store']),
            WithDraftsMiddleware::class
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        \Gate::authorize('create', Post::class);

        return Inertia::render('Shinrai/Create', [
            'canPublish' => request()->user()->can('publish', Post::class),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShinraiPostRequest $request)
    {
        // if user wants to publish but does not have capability to publish
        // or user cannot create
        \Gate::authorize('create', Post::class);
        if ($request->boolean('is_published') && $request->user()->cannot('publish', Post::class)) {
            abort(403);
        }
        //return $request->validated();
//        return $request->all();
        $post = Post::create($request->validated());

        $post->resources()->createMany($request->validated()['links']);

        if (!is_null($request->validated()['thumbnail_item'])) {
            $post->syncMedia($request->validated()['thumbnail_item']['id'], 'thumbnail');
        } else {
            $post->detachMediaTags('thumbnail');
        }

        $route = $request->validated()['metadata']['post_type'] == 'mv' ? 'mv.index' : 'album.index';

        return redirect()->route($route)->banner('Post created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        \Gate::authorize('update', $post);

        return Inertia::render('Shinrai/Create', [
            'post' => $post->load(['author', 'links', 'media'])->append('thumbnail_item'),
            'canPublish' => request()->user()->can('publish', $post),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Post $post, StorePostRequest $request)
    {
        \Gate::authorize('update', $post);
        if ($request->boolean('is_published') && $request->user()->cannot('publish', Post::class)) {
            abort(403);
        }
        $validated = $request->validated();

        $linksss = collect($validated['links'])->map(function ($item) {
            return array_filter([
                'id' => $item['id'] ?? null,  // This will return null if 'id' does not exist, making the key potentially removable
                'name' => $item['name'],
                'type' => $item['type'],
                'value' => json_encode($item['value'])
            ], function ($value, $key) {
                // Filter out the 'id' key if the value is null
                return !(is_null($value) && $key === 'id');
            }, ARRAY_FILTER_USE_BOTH);
        });

        $post->update($validated);

        $post->links()->upsert($linksss->toArray(), uniqueBy: ['id'], update: ['name', 'value']);

        if (!is_null($request->validated()['thumbnail_item'])) {
            $post->syncMedia($request->validated()['thumbnail_item']['id'], 'thumbnail');
        } else {
            $post->detachMediaTags('thumbnail');
        }

        return redirect()->back()->banner('Episode updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //dd($post);
        \Gate::authorize('delete', $post);
        $post->delete();

        return redirect()->back()->banner('Episode deleted successfully!');
    }
}

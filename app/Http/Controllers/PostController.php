<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Anime;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class PostController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['show', 'store']),
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
        return Inertia::render('Anime/Post/Create', [
            'anime' => $anime,
            'canPublish' => request()->user()->can('publish', Post::class),
            'bwang' => request()->user()->can('create', Post::class),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Anime $anime, StorePostRequest $request)
    {
        $post = $anime->posts()->create($request->validated());

        $post->resources()->createMany($request->validated()['resources']);

        return redirect()->route('post.show',[$anime, $post])->banner('Episode created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Anime $anime, Post $post)
    {
        return Inertia::render('Anime/Post/Show', [
            'anime' => $anime,
            'post' => $post->load('author', 'links')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}

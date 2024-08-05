<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnimeRequest;
use App\Models\Anime;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class AnimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response | LengthAwarePaginator
    {
        $anime = fn () => QueryBuilder::for(Anime::class)
            ->allowedSorts('id', 'title')
            ->paginate($request->integer('perPage'))
            ->appends(request()->query());
        return Inertia::render("Anime/Index", [
            'anime' => $anime,
            'canCreate' => fn () => auth()->user()->can('create', Anime::class)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): \Inertia\Response
    {
        if ($request->user()->cannot('create', Anime::class)) {
            abort(403);
        }

        return Inertia::render("Anime/Create", [
            'canPublish' => $request->user()->can('publish', Anime::class)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnimeRequest $request)
    {
        if ($request->boolean('is_published') && $request->user()->cannot('publish', Anime::class)) {
            abort(403);
        }

        Anime::create(
            $request->validated()
        );

        return redirect()->route('anime.index')->banner('Anime '. ($request->boolean('is_published') ? 'published' : 'draft saved') . ' successfully.');
    }

    /**
     * Publish resource in storage.
     */
    public function publish(Request $request, Anime $anime)
    {
        return $request;
    }

    /**
     * Display the specified resource.
     */
    public function show(Anime $anime)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Anime $anime)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Anime $anime)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anime $anime)
    {
        //
    }
}

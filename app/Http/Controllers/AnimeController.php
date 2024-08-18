<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnimeRequest;
use App\Models\Anime;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class AnimeController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response|LengthAwarePaginator
    {
        $perPage = $request->integer('perPage', 15);
        $anime = QueryBuilder::for(Anime::class);
        if ($perPage === -1) {
            $results = $anime->get();
            $anime = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $anime = $anime->paginate($perPage);
        }
        $anime->appends(request()->query());
        return Inertia::render("Anime/Index", [
            'anime' => fn() => $anime,
            'canCreate' => fn() => auth()->check() && auth()->user()->can('create', Anime::class),
            'canViewUnpublished' => fn() => auth()->check() && auth()->user()->can('viewAny')
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

        return redirect()->route('anime.index')->banner('Anime ' . ($request->boolean('is_published') ? 'published' : 'draft saved') . ' successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Anime $anime)
    {
        return Inertia::render('Anime/Show', [
            'anime' => $anime
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Anime $anime)
    {
        if (auth()->user()->cannot('update', $anime)) {
            abort(403);
        }

        return Inertia::render('Anime/Create', [
            'anime' => $anime,
            'canPublish' => auth()->user()->can('publish', $anime)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreAnimeRequest $request, Anime $anime)
    {
        if ($request->boolean('is_published') && $request->user()->cannot('publish', Anime::class)) {
            abort(403);
        }

        $anime->update($request->validated());

        return redirect()->route('anime.index')->banner('Anime updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anime $anime)
    {
        $anime->delete();

        return redirect()->back()->banner('Anime successfully deleted.');
    }
}

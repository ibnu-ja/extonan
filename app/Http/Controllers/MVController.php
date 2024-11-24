<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Oddvalue\LaravelDrafts\Http\Middleware\WithDraftsMiddleware;
use Spatie\QueryBuilder\QueryBuilder;

class MVController extends Controller
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['index', 'show']),
            WithDraftsMiddleware::class
        ];
    }

    public function index(Request $request): Response
    {
        $perPage = $request->integer('perPage', 15);

        $mv = QueryBuilder::for(Post::where('metadata->post_type', 'mv')->with('author')->visible());
        if ($perPage === -1) {
            $results = $mv->get();
            $mv = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $mv = $mv->paginate($perPage);
        }
        $mv->appends(request()->query());
        return Inertia::render('MV/Index', [
            'mv' => fn() => $mv,
            'canCreate' => fn() => auth()->check() && auth()->user()->can('create', Post::class),
            'canViewUnpublished' => fn() => auth()->check() && auth()->user()->can('viewAny')
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        return redirect()->route('shinrai.create', [
            'type' => 'mv'
        ]);
    }

    public function show(Request $request, Post $mv): Response
    {
        \Gate::authorize('view', $mv);

        return Inertia::render('MV/Show', [
            'post' => $mv->load('author', 'links'),
            'canPublish' => fn() => auth()->check() && auth()->user()->can('publish', $mv),
        ]);
    }

    public function edit(Request $request, Post $post): RedirectResponse
    {
        \Gate::authorize('update', $post);
        abort(501);
    }
}

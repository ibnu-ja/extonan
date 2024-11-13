<?php

namespace App\Http\Controllers;

use App\Models\Post;
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

    public function create(Request $request): Response
    {
        \Gate::authorize('create', Post::class);
        abort(501);
    }

    public function store(Request $request): Response
    {
        \Gate::authorize('create', Post::class);
        abort(501);
        //return Inertia::render('MV/Index');
    }

    public function show(Request $request, Post $post): Response
    {
        \Gate::authorize('view', $post);
        abort(501);
    }

    public function edit(Request $request, Post $post): Response
    {
        \Gate::authorize('update', $post);
        abort(501);
    }

    public function update(Request $request, Post $post): Response
    {
        \Gate::authorize('update', $post);
        abort(501);
    }

    public function destroy(Request $request, Post $post): Response
    {
        \Gate::authorize('delete', $post);
        abort(501);
    }
}

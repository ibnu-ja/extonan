<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Inertia\Inertia;
use Inertia\Response;
use Oddvalue\LaravelDrafts\Http\Middleware\WithDraftsMiddleware;
use Spatie\QueryBuilder\QueryBuilder;

class AlbumController extends Controller implements HasMiddleware {
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

    public function index(Request $request): Response
    {
        $perPage = $request->integer('perPage', 15);

        $albums = QueryBuilder::for(Post::whereIn('metadata->post_type', ['album', 'single'])->with('author')->visible());
        if ($perPage === -1) {
            $results = $albums->get();
            $albums = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $albums = $albums->paginate($perPage);
        }
        $albums->appends(request()->query());
        return Inertia::render('Album/Index', [
            'albums' => fn() => $albums,
            'canCreate' => fn() => auth()->check() && auth()->user()->can('create', Post::class),
            'canViewUnpublished' => fn() => auth()->check() && auth()->user()->can('viewAny')
        ]);
    }

    public function show(Request $request, Post $album): Response
    {
        \Gate::authorize('view', $album);

        return Inertia::render('Album/Show', [
            'album' => $album->load('author', 'links'),
            'canPublish' => fn() => auth()->check() && auth()->user()->can('publish', $album),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        return redirect()->route('shinrai.create', [
            'type' => 'album'
        ]);
    }
}

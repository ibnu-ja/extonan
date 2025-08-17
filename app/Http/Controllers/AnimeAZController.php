<?php

namespace App\Http\Controllers;

use App;
use App\Models\Anime;
use App\Models\Post;
use Inertia\Inertia;

class AnimeAZController extends Controller
{
    public function __invoke() {
        $anime = Anime::orderBy('title->'.App::currentLocale())->get();
        return Inertia::render('Anime/AZ', [
            'anime' => fn() => $anime,
            'canCreate' => fn() => auth()->check() && auth()->user()->can('create', Post::class),
            'canViewUnpublished' => fn() => auth()->check() && auth()->user()->can('viewAny')
        ]);

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Inertia\Inertia;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $albums = Album::select('id', 'name', 'name_real', 'name_trans', 'created_at', 'updated_at', 'slug')->latest()->paginate();

        // foreach ($albums as $album) {
        //     $album->cover->only('original_url');
        // }

        $albums->append('cover');
        return Inertia::render('Album/Index', ['album' => $albums]);
    }

    public function show(Album $album)
    {
        return Inertia::render('Album/Show', ['album' => $album]);
    }
}

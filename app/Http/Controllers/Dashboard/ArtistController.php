<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArtistFromVgmdbReqeust;
use App\Http\Requests\StoreArtistRequest;
use App\Models\Artist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $artist = Artist::select('id', 'name', 'name_real', 'name_trans', 'created_at', 'updated_at')->latest()->get();

        return Inertia::render('Dashboard/Artist/Index', ['artist' => $artist]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Dashboard/Artist/Edit', ['sex' => Artist::select('sex')->groupBy('sex')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreArtistRequest $request)
    {
        Artist::create($request->validated());
        return redirect()->route('artist.index')->with('snackbar', [
            'message' => 'Success storing data',
            'color'    => 'info',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function show(Artist $artist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function edit(Artist $artist)
    {
        return Inertia::render('Dashboard/Artist/Edit', ['artist' => $artist]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Artist $artist)
    {
        $artist->update($request->validated());
        return redirect()->route('artist.index')->with('snackbar', [
            'message' => 'Success updating data',
            'color'    => 'info',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Artist $artist)
    {
        $artist->delete();
        return redirect()->back()->with('snackbar', [
            'message' => 'Success deleting data',
            'color'    => 'info',
        ]);
    }

    /**
     * Display a listing of the resource in JSON (usage for album artist insertion).
     *
     * @return \App\Models\Artist
     */
    public function indexJson()
    {
        $artist = Artist::select('id', 'name', 'name_real')->get();
        return $artist;
    }

    /**
     * Update or Insert from name.
     */
    public function insertion(StoreArtistFromVgmdbReqeust $request)
    {
        $validated = $request->validated();
        $meta = collect(['vgmdb_link' => isset($validated['link']) ? $validated['link'] : null]);
        $artist = Artist::firstOrCreate(
            ['name' => $validated['names']['en']],
            [
                'name_real' => isset($validated['names']['ja']) ? $validated['names']['ja'] : null,
                'meta' => $meta
            ]
        );
        return $artist;
    }
}

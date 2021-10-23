<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlbumRequest;
use App\Models\Album;
use App\Models\Artist;
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
        $album = Album::latest()->get();
        return Inertia::render('Dashboard/Album/Index', ['album' => $album]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $artists = Artist::select('id', 'name', 'name_real')->get();
        return Inertia::render('Dashboard/Album/Edit', ['artists' => $artists]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAlbumRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAlbumRequest $request)
    {
        // return $request;
        $album = Album::create($request->all());
        foreach ($request['roles'] as $role => $artists_ids) {
            foreach ($artists_ids as $artists_id) {
                $album->artists()->attach($artists_id, ['role' => $role]);
            }
        }
        return redirect()->route('album.index')->with('snackbar', [
            'message' => 'Success storing data',
            'color'    => 'info',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function show(Album $album)
    {
        // return Inertia::render('Dashboard/Album/Edit', ['album' => $album]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function edit(Album $album)
    {
        $artists = Artist::select('id', 'name', 'name_real')->get();
        $album->append('roles');
        return Inertia::render('Dashboard/Album/Edit', [
            'album' => $album,
            'artists' => $artists
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreAlbumRequest  $request
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function update(StoreAlbumRequest $request, Album $album)
    {
        $album->update($request->all());
        $album->artists()->detach();
        foreach ($request['roles'] as $role => $artists_ids) {
            foreach ($artists_ids as $artists_id) {
                $album->artists()->attach($artists_id, ['role' => $role]);
            }
        }
        return redirect()->route('album.index')->with('snackbar', [
            'message' => 'Success updating data',
            'color'    => 'info',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function destroy(Album $album)
    {
        $album->delete();
        return redirect()->back()->with('snackbar', [
            'message' => 'Success deleting data',
            'color'    => 'info',
        ]);
    }
}

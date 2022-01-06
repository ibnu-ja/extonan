<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlbumRequest;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Product;
use Illuminate\Http\Request;
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
        $products = Product::select('id', 'name', 'name_real')->get();
        $organizations = Organization::select('id', 'name', 'name_real')->get();
        $events = Event::select('id', 'name', 'name_real')->get();
        return Inertia::render('Dashboard/Album/New',  [
            'artists' => $artists,
            'organizations' => $organizations,
            'products' => $products,
            'events' => $events,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAlbumRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAlbumRequest $request)
    {
        $album = Album::create($request->all());

        if ($request->roles) {
            foreach ($request->roles as $role => $artists_ids) {
                foreach ($artists_ids as $artists_id) {
                    $album->artists()->attach($artists_id, ['role' => $role]);
                }
            }
        }
        if ($request->orgs) {
            foreach ($request->orgs as $role => $org_ids) {
                foreach ($org_ids as $org_id) {
                    $album->organizations()->attach($org_id, ['role' => $role]);
                }
            }
        }
        foreach ($request->images as $image) {
            $album->addMedia($image)->toMediaCollection('gallery');
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
        $organizations = Organization::select('id', 'name', 'name_real')->get();
        $products = Product::select('id', 'name', 'name_real')->get();
        $events = Event::select('id', 'name', 'name_real')->get();
        $album->append('roles', 'orgs', 'uploadedGallery');
        return Inertia::render('Dashboard/Album/Edit', [
            'album' => $album,
            'artists' => $artists,
            'organizations' => $organizations,
            'products' => $products,
            'events' => $events,
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
        $album->organizations()->detach();
        foreach ($request['roles'] as $role => $artists_ids) {
            foreach ($artists_ids as $artists_id) {
                $album->artists()->attach($artists_id, ['role' => $role]);
            }
        }
        foreach ($request['orgs'] as $role => $org_ids) {
            foreach ($org_ids as $org_id) {
                $album->organizations()->attach($org_id, ['role' => $role]);
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

    public function destroyGallery(Album $album, $imageIndex)
    {

        $mediaItem = $album->getMedia('gallery');
        $mediaItem[$imageIndex]->delete();
        return redirect()->back()->with('snackbar', [
            'message' => 'Success deleting data',
            'color'    => 'info',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAlbumGalleryRequest;
use App\Http\Requests\StoreAlbumRequest;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Product;
use App\Services\AlbumService;
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
    public function store(StoreAlbumRequest $request, AlbumService $albumService)
    {
        $data = $request->validated();
        $album = Album::create($data);

        try {
            if (isset($data['roles'])) $albumService->createArtistRolesForAlbum($album, $data['roles']);
            if (isset($data['orgs'])) $albumService->createOrgRolesForAlbum($album, $data['orgs']);
            if (isset($data['images'])) $albumService->storeImagesToGallery($album, $data['images'], $data['imageLabel']);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 422);
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
    public function update(StoreAlbumRequest $request, Album $album, AlbumService $albumService)
    {
        $data = $request->validated();
        $album->update($data);
        $album->artists()->detach();
        $album->organizations()->detach();

        try {
            if (isset($data['roles'])) $albumService->createArtistRolesForAlbum($album, $data['roles']);
            if (isset($data['orgs'])) $albumService->createOrgRolesForAlbum($album, $data['orgs']);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 422);
        }
        return redirect()->route('album.index')->with('snackbar', [
            'message' => 'Success updating data',
            'color'    => 'info',
        ]);
    }

    public function storeGallery(Album $album, StoreAlbumGalleryRequest $request, AlbumService $albumService)
    {
        $data = $request->validated();

        try {
            if (isset($data['images'])) $albumService->storeImagesToGallery($album, $data['images'], $data['imageLabel']);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 422);
        }

        return redirect()->route('album.index')->with('snackbar', [
            'message' => 'Images stored',
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

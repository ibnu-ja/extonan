<?php

namespace App\Http\Controllers;

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

        $artist = Artist::latest()->get();
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'name_real' => 'string',
            'name_trans' => 'string',
            'birthdate' => 'date',
            'birthplace' => 'string',
            // TODO: artist meta validation
            // 'meta' => 'json|sometimes',
            'desc' => 'string',
            'sex' => 'alpha',
        ]);
        Artist::create($validated);
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
        $validated = $request->validate([
            'name' => 'required|string',
            'name_real' => 'string',
            'name_trans' => 'string',
            'birthdate' => 'date',
            'birthplace' => 'string',
            // TODO: artist meta validation
            // 'meta' => 'json|sometimes',
            'desc' => 'string|nullable',
            'sex' => 'alpha',
        ]);
        $artist->update($validated);
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
        //
    }
}

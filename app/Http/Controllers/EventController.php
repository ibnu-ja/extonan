<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $event = Event::latest()->get();
        return Inertia::render('Dashboard/Event/Index', ['event' => $event]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Dashboard/Event/Edit');
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
            'name' => 'string',
            'name_real' => 'string|nullable',
            'abbr' => 'string|nullable',
            'desc' => 'string|nullable',
            'held_from' => 'date|nullable',
            'held_to' => 'date|nullable',
        ]);
        Event::create($validated);
        return redirect()->route('event.index')->with('snackbar', [
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
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        return Inertia::render('Dashboard/Event/Edit', ['event' => $event]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        //FIXME validation fuck
        $validated = $request->validate([
            'name' => 'string',
            'name_real' => 'string|nullable',
            'abbr' => 'string|nullable',
            'desc' => 'string|nullable',
            'held_from' => 'date|nullable',
            'held_to' => 'date|nullable',
        ]);
        $event->update($validated);
        return redirect()->route('event.index')->with('snackbar', [
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
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->back()->with('snackbar', [
            'message' => 'Success deleting data',
            'color'    => 'info',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
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
    public function store(StoreEventRequest $request)
    {
        Event::create($request->validated());
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
    public function update(StoreEventRequest $request, Event $event)
    {
        $event->update($request->validated());
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

    /**
     * Display a listing of the resource in JSON (usage for album artist insertion).
     *
     * @return \App\Models\Event
     */
    public function indexJson()
    {
        $event = Event::select('id', 'name')->get();
        return $event;
    }

    /**
     * Update or Insert from name.
     */
    public function insertion(StoreEventRequest $request)
    {
        $validated = $request->validated();
        $meta = collect(['vgmdb_link' => isset($validated['link']) ? $validated['link'] : null]);
        $event = Event::firstOrCreate(
            ['name' => $validated['name']],
            [
                'held_from' => isset($validated['held_from']) ? $validated['held_from'] : null,
                'held_to' => isset($validated['held_to']) ? $validated['held_to'] : null,
                'meta' => $meta
            ]
        );
        return $event;
    }
}

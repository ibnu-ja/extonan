<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrganizationFromVgmdbRequest;
use App\Http\Requests\StoreOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organization  = Organization::latest()->get();
        return Inertia::render('Dashboard/Organization/Index', ['organization' => $organization]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render(
            'Dashboard/Organization/Edit',
            [
                'types' => Organization::select('type')->groupBy('type')->whereNotNull('type')->get()->pluck('type'),
                'regions' => Organization::select('region')->groupBy('region')->whereNotNull('region')->get()->pluck('region')
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrganizationRequest $request)
    {
        Organization::create($request->validated());
        return redirect()->route('organization.index')->with('snackbar', [
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
    public function show(Organization $organization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function edit(Organization $organization)
    {
        return Inertia::render(
            'Dashboard/Organization/Edit',
            [
                'organization' => $organization,
                'types' => Organization::select('type')->groupBy('type')->whereNotNull('type')->get()->pluck('type'),
                'regions' => Organization::select('region')->groupBy('region')->whereNotNull('region')->get()->pluck('region')
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function update(StoreOrganizationRequest $request, Organization $organization)
    {
        $organization->update($request->validated());
        return redirect()->route('organization.index')->with('snackbar', [
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
    public function destroy(Organization $organization)
    {
        $organization->delete();
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
        $artist = Organization::select('id', 'name')->get();
        return $artist;
    }

    /**
     * Update or Insert from vgmdb.
     */
    public function insertion(StoreOrganizationFromVgmdbRequest $request)
    {
        $validated = $request->validated();
        $meta = collect(['vgmdb_link' => isset($validated['link']) ? $validated['link'] : null]);
        $organization = Organization::firstOrCreate(
            [
                'name' => $validated['names']['en']
            ],
            [
                'name_real' => isset($validated['names']['ja']) ? $validated['names']['ja'] : null,
                'name_trans' =>  $validated['names']["ja-latn"],
                'meta' => $meta
            ]
        );
        return $organization;
    }
}

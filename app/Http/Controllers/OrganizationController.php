<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    //TODO: move validation to FormRequest
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'name_real' => 'string|nullable',
            'desc' => 'string|nullable',
            'type' => 'string|nullable',
            'region' => 'string|nullable',
            'meta.*' => 'string|nullable',
        ]);
        Organization::create($validated);
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
    public function update(Request $request, Organization $organization)
    {
        //FIXME validation fuck
        $validated = $request->validate([
            'name' => 'required|string',
            'name_real' => 'string|nullable',
            'desc' => 'string|nullable',
            'type' => 'string|nullable',
            'region' => 'string|nullable',
            'meta.*' => 'string|nullable',
        ]);
        $organization->update($validated);
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
}

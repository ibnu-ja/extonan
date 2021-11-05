<?php
// TODO: product type untuk edit dan store

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\StoreProductRequestFromVgmdbRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product = Product::latest()->get();
        return Inertia::render('Dashboard/Product/Index', ['product' => $product]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render(
            'Dashboard/Product/Edit',
            ['types' => Product::select('type')->groupBy('type')->whereNotNull('type')->get()->pluck('type')]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->all();

        Product::create($validated);
        return redirect()->route('product.index')->with('snackbar', [
            'message' => 'Success storing data',
            'color'    => 'info',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return Inertia::render(
            'Dashboard/Product/Edit',
            [
                'types' => Product::select('type')->groupBy('type')->whereNotNull('type')->get()->pluck('type'),
                'product' => $product
            ],
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProductRequest $request, Product $product)
    {
        $validated = $request->all();

        $product->update($validated);
        // return $product;
        return redirect()->route('product.index')->with('snackbar', [
            'message' => 'Success updating data',
            'color'    => 'info',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('product.index')->with('snackbar', [
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
        $products = Product::select('id', 'name', 'name_real')->get();
        return $products;
    }

    /**
     * Update or Insert from name.
     */
    public function insertion(StoreProductRequestFromVgmdbRequest $request)
    {
        $validated = $request->all();
        $meta = collect(['vgmdb_link' => isset($validated['link']) ? $validated['link'] : null]);
        $product = Product::firstOrCreate(
            ['name' => $validated['names']['en']],
            [
                'name_real' => isset($validated['names']['ja']) ? $validated['names']['ja'] : null,
                'meta' => $meta
            ]
        );
        return $product;
    }
}

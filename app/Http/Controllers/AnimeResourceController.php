<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Post;
use App\Models\Resource;

class AnimeResourceController extends Controller
{
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anime $anime, Post $post, Resource $resource)
    {
        \Gate::authorize('delete', $post);
        $resource->delete();

        return redirect()->back()->with('post')->banner('Link deleted successfully!');
    }
}

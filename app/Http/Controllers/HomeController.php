<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Show homepage
     */
    public function __invoke(): \Inertia\Response
    {
        return Inertia::render('Home/Index', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'latestAnime' => Anime::with('author')->take(5)->orderBy('published_at')->get()
        ]);
    }
}

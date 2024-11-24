<?php

use App\Http\Controllers\AnimeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MVController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ShinraiPostController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class)->name('home');

Route::resource('/anime/{anime}/post', PostController::class)->except('index');
Route::resource('/mv', MVController::class);
Route::resource('/shinrai', ShinraiPostController::class)->except('index', 'show');

Route::resources([
    '/anime' => AnimeController::class
]);

Route::middleware('auth')->group(function () {
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::get('media-months',
        [MediaController::class, 'getMonths'])->name('media.months');
    Route::post('media',
        [MediaController::class, 'store'])->name('media.store');
    Route::delete('media/{media}',
        [MediaController::class, 'destroy'])->name('media.destroy');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
require __DIR__.'/socialstream.php';

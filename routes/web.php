<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MVController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::get('media-months', [MediaController::class, 'getMonths'])->name('media.months');
    Route::get('media-months-counts', [MediaController::class, 'getMonthsWithCounts'])->name('media.months-counts');
    Route::post('media', [MediaController::class, 'store'])->name('media.store');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
});

Route::get('playground', function () {
    abort_unless(config('app.debug'), 404);

    return Inertia::render('Media/Playground');
})->name('playground');

Route::resource('/anime', AnimeController::class);
Route::get('/anime/az', [AnimeController::class, 'az'])->name('anime.az');
Route::resource('/anime/{anime}/post', PostController::class)->except('index');
Route::resource('/album', AlbumController::class);
Route::resource('/mv', MVController::class);

require __DIR__.'/settings.php';

<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MVController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::resource('/anime', AnimeController::class);
Route::get('/anime/az', [AnimeController::class, 'az'])->name('anime.az');
Route::resource('/anime/{anime}/post', PostController::class)->except('index');
Route::resource('/album', AlbumController::class);
Route::resource('/mv', MVController::class);

require __DIR__.'/settings.php';

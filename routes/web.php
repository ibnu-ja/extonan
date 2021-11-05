<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProductController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::get('/event/reindex', [EventController::class, 'indexJson'])->name('event.indexJson');
    Route::post('/event/insertion', [EventController::class, 'insertion'])->name('event.insertVgmdb');
    Route::get('/artist/reindex', [ArtistController::class, 'indexJson'])->name('artist.indexJson');
    Route::post('/artist/insertion', [ArtistController::class, 'insertion'])->name('artist.insertVgmdb');
    Route::get('/organization/reindex', [OrganizationController::class, 'indexJson'])->name('organization.indexJson');
    Route::post('/organization/insertion', [OrganizationController::class, 'insertion'])->name('organization.insertVgmdb');
    Route::get('/product/reindex', [ProductController::class, 'indexJson'])->name('product.indexJson');
    Route::post('/product/insertion', [ProductController::class, 'insertion'])->name('product.insertVgmdb');
    // Route::get('/organization/test', [OrganizationController::class, 'insertion'])->name('organization.testing');
    Route::resources([
        'album' => AlbumController::class,
        'artist' => ArtistController::class,
        'product' => ProductController::class,
        'event' => EventController::class,
        'organization' => OrganizationController::class
    ]);
    // Route::post('/artist/insertion', [ArtistController::class, 'insertion'])->name('artist.insertVgmdb');
});

// Route::get('/dashboard/album', [AlbumController::class, 'index']);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

// require_once __DIR__ . '/fortify.php';
// require_once __DIR__ . '/jetstream_inertia.php';
require_once __DIR__ . '/socialstream.php';

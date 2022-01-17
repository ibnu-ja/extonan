<?php

use App\Http\Controllers\AlbumController as AlbumController;
use App\Http\Controllers\Dashboard\AlbumController as DashboardAlbumController;
use App\Http\Controllers\Dashboard\ArtistController as DashboardArtistController;
use App\Http\Controllers\Dashboard\EventController as DashboardEventController;
use App\Http\Controllers\Dashboard\OrganizationController as DashboardOrganizationController;
use App\Http\Controllers\Dashboard\ProductController as DashboardProductController;
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

Route::get('/album', [AlbumController::class, 'index'])->name('album.index');

Route::middleware('auth')->name('dashboard.')->prefix('dashboard')->group(function () {
    Route::get('/event/reindex', [DashboardEventController::class, 'indexJson'])->name('event.indexJson');
    Route::post('/event/insertion', [DashboardEventController::class, 'insertion'])->name('event.insertVgmdb');
    Route::get('/artist/reindex', [DashboardArtistController::class, 'indexJson'])->name('artist.indexJson');
    Route::post('/artist/insertion', [DashboardArtistController::class, 'insertion'])->name('artist.insertVgmdb');
    Route::get('/organization/reindex', [DashboardOrganizationController::class, 'indexJson'])->name('organization.indexJson');
    Route::post('/organization/insertion', [DashboardOrganizationController::class, 'insertion'])->name('organization.insertVgmdb');
    Route::get('/product/reindex', [DashboardProductController::class, 'indexJson'])->name('product.indexJson');
    Route::post('/product/insertion', [DashboardProductController::class, 'insertion'])->name('product.insertVgmdb');
    Route::post('/album/{album}/gallery', [DashboardAlbumController::class, 'storeGallery'])->name('album.storeGallery');
    Route::delete('/album/{album}/gallery/{index}', [DashboardAlbumController::class, 'destroyGallery'])->name('album.destroyGallery');
    // Route::get('/organization/test', [OrganizationController::class, 'insertion'])->name('organization.testing');
    Route::resources([
        'album' => DashboardAlbumController::class,
        'artist' => DashboardArtistController::class,
        'product' => DashboardProductController::class,
        'event' => DashboardEventController::class,
        'organization' => DashboardOrganizationController::class
    ]);
});

// Route::get('/dashboard/album', [AlbumController::class, 'index']);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

// require_once __DIR__ . '/fortify.php';
// require_once __DIR__ . '/jetstream_inertia.php';
require_once __DIR__ . '/socialstream.php';

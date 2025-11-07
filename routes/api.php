<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\SpotifyController;
use Illuminate\Http\Request; 

// Rutas públicas
Route::get('/test', function () {
    return response()->json([
        'message' => 'API funcionando correctamente',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Rutas de Spotify (públicas para autenticación)
Route::prefix('spotify')->name('spotify.')->group(function () {
    Route::get('/login', [SpotifyController::class, 'login'])->name('login');
    Route::get('/auth-url', [SpotifyController::class, 'getAuthUrl'])->name('auth-url');
    Route::get('/callback', [SpotifyController::class, 'callback'])->name('callback');
    Route::get('/session-status', [SpotifyController::class, 'getSessionStatus'])->name('session.status');
    Route::get('/debug-config', [SpotifyController::class, 'debugConfig'])->name('debug.config'); // Para debugging
});

// Rutas protegidas con autenticación
Route::middleware('auth:sanctum')->group(function () {
    // CRUDs protegidos
    Route::apiResource('usuario', UsuarioController::class);
    Route::apiResource('rol', RolController::class);    
    Route::apiResource('publicacion', PublicacionController::class);
    Route::apiResource('proyecto', ProyectoController::class);
    
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Spotify rutas protegidas (requieren autenticación de Spotify)
    Route::prefix('spotify')->name('spotify.')->group(function () {
        Route::post('/logout', [SpotifyController::class, 'logout'])->name('logout');
        Route::get('/me', [SpotifyController::class, 'profile'])->name('profile');
        Route::get('/playlists', [SpotifyController::class, 'getPlaylists'])->name('playlists.index');
        Route::get('/playlists/{id}', [SpotifyController::class, 'getPlaylist'])->name('playlists.show');
        Route::post('/playlists', [SpotifyController::class, 'createPlaylist'])->name('playlists.create');
        Route::put('/playlists/{id}', [SpotifyController::class, 'updatePlaylist'])->name('playlists.update');
        Route::get('/playlists/{id}/tracks', [SpotifyController::class, 'getPlaylistTracks'])->name('playlists.tracks');
        Route::get('/search', [SpotifyController::class, 'searchTracks'])->name('search');
        Route::post('/playlists/{id}/tracks', [SpotifyController::class, 'addTracksToPlaylist'])->name('playlists.addTracks');
        Route::delete('/playlists/{id}/tracks', [SpotifyController::class, 'removeTracksFromPlaylist'])->name('playlists.removeTracks');
    });
});
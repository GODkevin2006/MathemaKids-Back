<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\SpotifyController;

// Rutas CRUD principales de tu aplicación
Route::apiResource('usuario', UsuarioController::class);
Route::apiResource('rol', RolController::class);    
Route::apiResource('publicacion', PublicacionController::class);
Route::apiResource('proyecto', ProyectoController::class);
Route::apiResource('categoria', CategoriaController::class);

// Rutas de Spotify (separadas y agrupadas)
Route::prefix('spotify')->group(function () {
    // Autenticación
    Route::get('/login', [SpotifyController::class, 'login']);
    Route::get('/callback', [SpotifyController::class, 'callback']);
    Route::get('/logout', [SpotifyController::class, 'logout']);
    
    // Estado de sesión
    Route::get('/session-status', [SpotifyController::class, 'getSessionStatus']);
    
    // Perfil de usuario
    Route::get('/me', [SpotifyController::class, 'profile']);
    Route::get('/dashboard', [SpotifyController::class, 'dashboard']);
    
    // Playlists
    Route::get('/playlists', [SpotifyController::class, 'getPlaylists']);
    Route::get('/playlists/{id}', [SpotifyController::class, 'getPlaylist']);
    Route::post('/playlists', [SpotifyController::class, 'createPlaylist']);
    Route::put('/playlists/{id}', [SpotifyController::class, 'updatePlaylist']);
    
    // Canciones de playlists
    Route::get('/playlists/{playlistId}/tracks', [SpotifyController::class, 'getPlaylistTracks']);
    Route::post('/playlists/{playlistId}/tracks', [SpotifyController::class, 'addTracksToPlaylist']);
    Route::delete('/playlists/{playlistId}/tracks', [SpotifyController::class, 'removeTracksFromPlaylist']);
    
    // Búsqueda
    Route::get('/search/tracks', [SpotifyController::class, 'searchTracks']);
});
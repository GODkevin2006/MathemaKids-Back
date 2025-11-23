<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContenidoProyectoController;
use App\Http\Controllers\SpotifyController;


// -------------------------------
//  RUTAS PUBLICAS (sin login)
// -------------------------------
Route::post('login', [AuthController::class, 'login']);
Route::post('/register', [UsuarioController::class, 'store']);

//RUTA  /me (ewquiere token pero no rol especifico)
Route::middleware(['jwt.cookie'])->group(function () {

    //Datos del usuario autenticado
    Route::get('/me', [AuthController::class, 'me']);

    //Cerrar sesion
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('publicacion', [PublicacionController::class, 'index']);
Route::get('publicacion/{id}', [PublicacionController::class, 'show']);

Route::get('proyecto', [ProyectoController::class, 'index']);
Route::get('proyecto/{id}', [ProyectoController::class, 'show']);

Route::get('contenido_proyecto', [ContenidoProyectoController::class, 'index']);
Route::get('contenido_proyecto/{id}', [ContenidoProyectoController::class, 'show']);

Route::get('categoria', [CategoriaController::class, 'index']);
Route::get('categoria/{id}', [CategoriaController::class, 'show']);



// -------------------------------
//  ADMIN (rol 1)
// -------------------------------
Route::middleware(['jwt.cookie', 'rol:1'])->group(function () {
    
    // Rutas específicas de usuario para admin (sin conflicto con rol 3)
    Route::get('usuario', [UsuarioController::class, 'index']);
    Route::post('usuario', [UsuarioController::class, 'store']);
    // Ver, editar y eliminar cualquier usuario
    Route::get('usuario/{id}', [UsuarioController::class, 'show']);
    Route::put('usuario/{id}', [UsuarioController::class, 'update']);
    Route::delete('usuario/{id}', [UsuarioController::class, 'destroy']);

    
    Route::apiResource('rol', RolController::class);
    Route::apiResource('usuario', UsuarioController::class);
    Route::apiResource('publicacion', PublicacionController::class);
    Route::apiResource('proyecto', ProyectoController::class);
    Route::apiResource('categoria', CategoriaController::class);
    Route::apiResource('contenido_proyecto', ContenidoProyectoController::class);
    
    // admin register
    
});



// -------------------------------
//  MODERADOR (rol 2)
// -------------------------------
Route::middleware(['jwt.cookie', 'rol:2'])->group(function () {
    
    Route::apiResource('publicacion', PublicacionController::class);
    Route::apiResource('proyecto', ProyectoController::class);
    Route::apiResource('categoria', CategoriaController::class);
    Route::apiResource('contenido_proyecto', ContenidoProyectoController::class);
});



// -------------------------------
//  USUARIO NORMAL (rol 3)
// -------------------------------
Route::middleware(['jwt.cookie', 'rol:3'])->group(function () {
    
    // Perfil propio
    Route::get('usuario/{id}', [UsuarioController::class, 'show']);
    Route::put('usuario/{id}', [UsuarioController::class, 'update']);
    Route::delete('usuario/{id}', [UsuarioController::class, 'destroy']);

    //publicaciones
    Route::apiResource('publicacion', PublicacionController::class);


    // Solo ver proyectos
    Route::get('proyecto', [ProyectoController::class, 'index']);
    Route::get('proyecto/{id}', [ProyectoController::class, 'show']);

    // Solo ver contenido proyecto
    Route::get('contenido_proyecto', [ContenidoProyectoController::class, 'index']);
    Route::get('contenido_proyecto/{id}', [ContenidoProyectoController::class, 'show']);

    // Solo ver categorias
    Route::get('categoria', [CategoriaController::class, 'index']);
    Route::get('categoria/{id}', [CategoriaController::class, 'show']);
});



// -------------------------------
//  SPOTIFY (se mantiene igual)
// -------------------------------
Route::prefix('spotify')->group(function () {
    Route::get('/login', [SpotifyController::class, 'login']);
    Route::get('/callback', [SpotifyController::class, 'callback']);
    Route::get('/logout', [SpotifyController::class, 'logout']);

    Route::get('/session-status', [SpotifyController::class, 'getSessionStatus']);

    Route::get('/me', [SpotifyController::class, 'profile']);
    Route::get('/dashboard', [SpotifyController::class, 'dashboard']);

    Route::get('/playlists', [SpotifyController::class, 'getPlaylists']);
    Route::get('/playlists/{id}', [SpotifyController::class, 'getPlaylist']);
    Route::post('/playlists', [SpotifyController::class, 'createPlaylist']);
    Route::put('/playlists/{id}', [SpotifyController::class, 'updatePlaylist']);

    Route::get('/playlists/{playlistId}/tracks', [SpotifyController::class, 'getPlaylistTracks']);
    Route::post('/playlists/{playlistId}/tracks', [SpotifyController::class, 'addTracksToPlaylist']);
    Route::delete('/playlists/{playlistId}/tracks', [SpotifyController::class, 'removeTracksFromPlaylist']);

    Route::get('/search/tracks', [SpotifyController::class, 'searchTracks']);
});

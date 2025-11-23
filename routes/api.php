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
use App\Http\Controllers\ResetPasswordApiController;


// -------------------------------
//  RUTAS PUBLICAS (sin login)
// -------------------------------
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [UsuarioController::class, 'store']);

Route::get('publicacion', [PublicacionController::class, 'index']);
Route::get('publicacion/{id}', [PublicacionController::class, 'show']);

Route::get('proyecto', [ProyectoController::class, 'index']);
Route::get('proyecto/{id}', [ProyectoController::class, 'show']);

Route::get('contenido_proyecto', [ContenidoProyectoController::class, 'index']);
Route::get('contenido_proyecto/{id}', [ContenidoProyectoController::class, 'show']);

Route::get('categoria', [CategoriaController::class, 'index']);
Route::get('categoria/{id}', [CategoriaController::class, 'show']);


// -------------------------------
//  RUTAS CON TOKEN (cualquier rol)
// -------------------------------
Route::middleware(['jwt.cookie'])->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});


// -------------------------------
//  ADMIN (rol 1)
// -------------------------------
Route::middleware(['jwt.cookie', 'rol:1'])->group(function () {

    // Usuarios (admin controla todo)
    Route::get('usuario', [UsuarioController::class, 'index']);
    Route::post('usuario', [UsuarioController::class, 'store']);
    // Ver, editar y eliminar cualquier usuario
    Route::get('usuario/{id}', [UsuarioController::class, 'show']);
    Route::put('usuario/{id}', [UsuarioController::class, 'update']);
    Route::delete('usuario/{id}', [UsuarioController::class, 'destroy']);

    // Admin puede gestionar todo el contenido
    Route::apiResource('rol', RolController::class)->except(['create', 'edit']);
    Route::apiResource('publicacion', PublicacionController::class)->except(['create', 'edit']);
    Route::apiResource('proyecto', ProyectoController::class)->except(['create', 'edit']);
    Route::apiResource('categoria', CategoriaController::class)->except(['create', 'edit']);
    Route::apiResource('contenido_proyecto', ContenidoProyectoController::class)->except(['create', 'edit']);
});



// -------------------------------
//  MODERADOR (rol 2)
// -------------------------------
Route::middleware(['jwt.cookie', 'rol:2'])->group(function () {
    Route::apiResource('publicacion', PublicacionController::class)->except(['create', 'edit']);
    Route::apiResource('proyecto', ProyectoController::class)->except(['create', 'edit']);
    Route::apiResource('categoria', CategoriaController::class)->except(['create', 'edit']);
    Route::apiResource('contenido_proyecto', ContenidoProyectoController::class)->except(['create', 'edit']);
});



// -------------------------------
//  USUARIO NORMAL (rol 3)
// -------------------------------
Route::middleware(['jwt.cookie', 'rol:3'])->group(function () {

    // Perfil propio
    Route::get('usuario/{id}', [UsuarioController::class, 'show']);
    Route::put('usuario/{id}', [UsuarioController::class, 'update']);
    Route::delete('usuario/{id}', [UsuarioController::class, 'destroy']);

    // Solo puede crear y manejar sus publicaciones
    Route::apiResource('publicacion', PublicacionController::class)->except(['create', 'edit']);

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
//  SPOTIFY (sin JWT, solo OAuth)
// -------------------------------
Route::prefix('spotify')->group(function () {

    // Autenticación con Spotify
    Route::get('/login', [SpotifyController::class, 'login']);
    Route::get('/callback', [SpotifyController::class, 'callback']);
    Route::get('/logout', [SpotifyController::class, 'logout']);

    // Estado de sesión
    Route::get('/session-status', [SpotifyController::class, 'getSessionStatus']);

    // Datos del usuario
    Route::get('/me', [SpotifyController::class, 'profile']);
    Route::get('/dashboard', [SpotifyController::class, 'dashboard']);

    // Playlists
    Route::get('/playlists', [SpotifyController::class, 'getPlaylists']);
    Route::get('/playlists/{id}', [SpotifyController::class, 'getPlaylist']);
    Route::post('/playlists', [SpotifyController::class, 'createPlaylist']);
    Route::put('/playlists/{id}', [SpotifyController::class, 'updatePlaylist']);

    Route::get('/playlists/{playlistId}/tracks', [SpotifyController::class, 'getPlaylistTracks']);
    Route::post('/playlists/{playlistId}/tracks', [SpotifyController::class, 'addTracksToPlaylist']);
    Route::delete('/playlists/{playlistId}/tracks', [SpotifyController::class, 'removeTracksFromPlaylist']);

    // Búsqueda
    Route::get('/search/tracks', [SpotifyController::class, 'searchTracks']);
});


// -------------------------------
//  RECUPERAR CONTRASEÑA
// -------------------------------
Route::post('password/forgot', [ResetPasswordApiController::class, 'sendResetLink']);
Route::post('password/reset', [ResetPasswordApiController::class, 'resetPassword']);

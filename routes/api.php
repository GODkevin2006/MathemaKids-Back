<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ContenidoProyectoController;
use App\Http\Controllers\SpotifyController;

// Rutas CRUD principales de la web
Route::apiResource('usuario', UsuarioController::class);
Route::apiResource('rol', RolController::class);    
Route::apiResource('publicacion', PublicacionController::class);
Route::apiResource('proyecto', ProyectoController::class);
Route::apiResource('categoria', CategoriaController::class);
Route::apiResource('contenido_proyecto', ContenidoProyectoController::class);


// rutas PUBLICAS para usuarios que no inicien sesión, pueden solo ver y listar
Route::get('publicacion', [PublicacionController::class, 'index']);
Route::get('publicacion/{id}', [PublicacionController::class, 'show']);

Route::get('proyecto', [ProyectoController::class, 'index']);
Route::get('proyecto/{id}', [ProyectoController::class, 'show']);

Route::get('contenido_proyecto', [ContenidoProyectoController::class, 'index']);
Route::get('contenido_proyecto/{id}', [ContenidoProyectoController::class, 'show']);

Route::get('categoria', [CategoriaController::class, 'index']);
Route::get('categoria/{id}', [CategoriaController::class, 'show']);


// rutas de el rol ADMIN protegidas, basicamente acceso a todo
Route::middleware(['auth:sanctum', 'rol:1'])->group(function () {
    Route::apiResource('usuario', UsuarioController::class);
    Route::apiResource('rol', RolController::class);
    Route::apiResource('publicacion', PublicacionController::class);
    Route::apiResource('proyecto', ProyectoController::class);
    Route::apiResource('categoria', CategoriaController::class);
    Route::apiResource('contenido_proyecto', ContenidoProyectoController::class);
});

// rutas de el rol MODERADOR protegidas acceso a toso pero solo de las que se ven
Route::middleware(['auth:sanctum', 'rol:2'])->group(function () {
    Route::apiResource('publicacion', PublicacionController::class);
    Route::apiResource('proyecto', ProyectoController::class);
    Route::apiResource('categoria', CategoriaController::class);
    Route::apiResource('contenido_proyecto', ContenidoProyectoController::class);
});

// rutas protegidas de USUARIO 
Route::middleware(['auth:sanctum', 'rol:3'])->group(function () {

    // Publicaciones propias
    Route::apiResource('publicacion', PublicacionController::class);

    // solo Ver proyectos y contenido de los mismos
    Route::get('proyecto', [ProyectoController::class, 'index']);
    Route::get('proyecto/{id}', [ProyectoController::class, 'show']);
    Route::get('contenido_proyecto', [ContenidoProyectoController::class, 'index']);
    Route::get('contenido_proyecto/{id}', [ContenidoProyectoController::class, 'show']);

    // solo Ver categorías que ya estén en la tabla
    Route::get('categoria', [CategoriaController::class, 'index']);
    Route::get('categoria/{id}', [CategoriaController::class, 'show']);
});



// admin rutas
Route::middleware(['auth:api','rol :1'])->group(function () {
    route::post('/register', [UsuarioController::class, 'register']);   
    route::post('/register', [SpotifyController::class, 'register']);  
    route::post('/register', [RolController::class, 'register']);  
    route::post('/register', [PublicacionController::class, 'register']);  
    route::post('/register', [ProyectoController::class, 'register']);  
    route::post('/register', [ContenidoProyectoController::class, 'register']);  
    route::post('/register', [CategoriaController::class, 'register']);  
});

Route::middleware(['auth:api','rol :2'])->group(function () {
    route::post('/register', [PublicacionController::class, 'register']);
    route::post('/register', [ProyectoController::class, 'register']); 
    route::post('/register', [ContenidoProyectoController::class, 'register']); 
    route::post('/register', [CategoriaController::class, 'register']);   
    route::post('/register', [SpotifyController::class, 'register']); 
});


Route::middleware(['auth:api','rol :3'])->group(function () {
    route::post('/register', [UsuarioController::class, 'register']); 
    route::post('/register', [PublicacionController::class, 'register']); 
});




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

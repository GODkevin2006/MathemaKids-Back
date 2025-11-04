<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
<<<<<<< HEAD
use App\Http\Controllers\PublicacionController;


Route::apiResource('usuario', UsuarioController::class);
Route::apiResource('rol', RolController::class);    
Route::apiResource('publicacion', PublicacionController::class);


=======
use App\Http\Controllers\ProyectoController;

Route::apiResource('usuario', UsuarioController::class);
Route::apiResource('rol', RolController::class);
Route::post('proyecto', [ProyectoController::class, 'store']);
>>>>>>> aff43ceae194a76987bfad307f927360d7dcb35d

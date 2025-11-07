<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ContenidoProyectoController;


Route::apiResource('usuario', UsuarioController::class);
    Route::apiResource('rol', RolController::class);    
    Route::apiResource('publicacion', PublicacionController::class);
    Route::apiResource('proyecto', ProyectoController::class);
    Route::apiResource('categoria', CategoriaController::class);
    Route::apiResource('contenidoproyecto', ContenidoProyectoController::class);
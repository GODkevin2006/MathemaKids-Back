<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\CategoriaController;


Route::apiResource('usuario', UsuarioController::class);
Route::apiResource('rol', RolController::class);    
Route::apiResource('publicacion', PublicacionController::class);
Route::apiResource('proyecto', ProyectoController::class);
Route::apiResource('categoria', CategoriaController::class);

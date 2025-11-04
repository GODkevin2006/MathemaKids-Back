<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\ProyectoController;

Route::apiResource('usuario', UsuarioController::class);
Route::apiResource('rol', RolController::class);
Route::post('proyecto', [ProyectoController::class, 'store']);

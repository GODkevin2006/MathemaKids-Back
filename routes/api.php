<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\ProyectoController;

Route::apiResource('usuario', UsuarioController::class);
Route::apiResource('rol', RolController::class);
Route::get('/proyecto', [ProyectoController::class, 'index']);
Route::post('/proyecto', [ProyectoController::class, 'store']);
Route::get('/proyecto/{id}', [ProyectoController::class, 'show']);
Route::put('/proyecto/{id}', [ProyectoController::class, 'update']);
Route::patch('/proyecto/{id}', [ProyectoController::class, 'update']);
Route::delete('/proyecto/{id}', [ProyectoController::class, 'destroy']);

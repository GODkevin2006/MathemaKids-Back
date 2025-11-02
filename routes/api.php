<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;

Route::apiResource('usuario', UsuarioController::class);
Route::apiResource('rol', RolController::class);    
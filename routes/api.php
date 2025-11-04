<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicacionController;

Route::apiResource('publicaciones', PublicacionController::class);



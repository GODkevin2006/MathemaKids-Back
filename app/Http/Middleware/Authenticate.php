<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;

class Authenticate extends Middleware
{
    
    public function redirecto ($request)
    {
        if ($request->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'message' => 'Acceso no autorizado. Debes inisiar sesion'
            ], 401));
    }

    return null;
    }
}
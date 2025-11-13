<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RolMiddleware
{
    public function handle(Request $request, Closure $next, $rol)
    {
        if(!$request->user() || $request->user()->id_rol !== $rol){
            return response()->json(['error' => 'Unauthjdhjhjgorized'], 403);
    }
    return $next($request);
}
}
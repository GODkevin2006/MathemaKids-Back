<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RolMiddleware
{
    public function handle(Request $request, Closure $next, $rol)
    {
<<<<<<< HEAD
        if(!$request->user() || $request->user()->id_rol !== $rol){
            return response()->json(['error' => 'Unauthorized'], 403);
=======
        // Verificar si hay usuario autenticado por JWT
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'No autenticado. Token inválido o ausente.'
            ], 401);
        }


        if ((string) $user->id_rol !== (string) $rol) {
            return response()->json([
                'error' => 'No autorizado. Tu rol no tiene acceso a esta ruta.',
            ], 403);
        }

        return $next($request);
>>>>>>> 62f2396224575b4c65d7cc7ec62661ac443ae08c
    }

}
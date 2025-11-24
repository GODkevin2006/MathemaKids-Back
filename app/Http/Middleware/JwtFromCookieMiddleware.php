<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtFromCookieMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Leer token desde la cookie "token"
            $token = $request->cookie('token');

            if (!$token) {
                return response()->json(['error' => 'Token no encontrado en la cookie'], 401);
            }

            // Establecer el token manualmente para JWTAuth
            JWTAuth::setToken($token);

            // Autenticar para que $request->user() funcione
            $user = JWTAuth::authenticate();

            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 401);
            }

            // Inyectar el usuario autenticado al request
            $request->setUserResolver(function () use ($user) {
                return $user;
            });
            
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }

        return $next($request);
    }
}

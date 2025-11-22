<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{

    public function login(AuthRequest $login)
{
    $credentials = [
        'correo'   => $login->input('correo'),
        'password' => $login->input('contraseña'),
    ];

    try {
        if (!$token = \Tymon\JWTAuth\Facades\JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'credenciales incorrectas'], 401);
        }
    } catch (JWTException $e) {
        return response()->json(['error' => 'Could not create token'], 500);
    }

    $user = JWTAuth::user();

    return response()->json([
        'message' => 'Login successful',
        'user'    => $user,
        'token'   => $token
    ])->cookie('token', $token, 60 * 24, null, null, false, true);
}

public function me(Request $request){
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ],200);
    }   

public function logout(){
    return response()->json([
        'success' => true,
        'message' => 'Sesion cerrada'
    ], 200)->cookie('token', '', -1);
}
}
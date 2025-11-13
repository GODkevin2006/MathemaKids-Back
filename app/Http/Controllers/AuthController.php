<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{

public function login(LoginRequest $login)
{
    $credentials =$login->only('correo', 'contraseña');

    try {
        if (!$token = JWTAuth::attempt($credentials)){
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
}catch(JWTException $e){
    return response()->json(['error' => 'Could not create token'], 500);
}

$user = JWTAuch::user();

return response()->json([
    'message' => 'Login successful',
    'role' => $user->rol,
    'user' => $user
])
->cookie('token',60*24, null, null, false, true);
}
}
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class ResetPasswordApiController extends Controller
{
    // 1. Solicitar recuperación
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'correo' => 'required|email'
        ]);

        $user = User::where('correo', $request->correo)->first();

        if (!$user) {
            return response()->json(['error' => 'El correo no está registrado'], 404);
        }

        // Crear token en tabla password_resets
        $token = Str::random(64);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->correo],
            [
                'email' => $user->correo,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Enviar correo
        Mail::raw("Tu token para restablecer contraseña es: $token", function ($msg) use ($user) {
            $msg->to($user->correo)
                ->subject('Restablecer contraseña');
        });

        return response()->json([
            'success' => 'Token enviado al correo',
            'token_debug' => $token // ← Elimínalo en producción
        ]);
    }

    // 2. Restablecer contraseña
    public function resetPassword(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->correo)
            ->first();

        if (!$reset) {
            return response()->json(['error' => 'No existe solicitud para este correo'], 404);
        }

        // Validar token
        if (!Hash::check($request->token, $reset->token)) {
            return response()->json(['error' => 'Token inválido'], 400);
        }

        // Actualizar contraseña en tu tabla usuario (campo: contraseña)
        User::where('correo', $request->correo)->update([
            'contraseña' => Hash::make($request->password)
        ]);

        // Borrar solicitud para evitar reusos
        DB::table('password_resets')->where('email', $request->correo)->delete();

        return response()->json([
            'success' => 'La contraseña se ha actualizado correctamente'
        ], 200);
    }
}

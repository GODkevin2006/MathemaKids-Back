<?php

namespace App\Services;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public static function crearUsuario($registroUsuario)
    {
        // Buscar el rol “Usuario” en la tabla rol
        $rol = Rol::where('nombre_rol', 'usuario')->first();

        if (!$rol) {
            throw new \Exception('No se encontró el rol "Usuario" en la tabla rol.');
        }

        // Crear usuario con rol asignado automáticamente
        return User::create([
            'nombres' => $registroUsuario['nombres'],
            'apellidos' => $registroUsuario['apellidos'],
            'correo' => $registroUsuario['correo'],
            'contraseña' => $registroUsuario['contraseña'],
            'id_rol' => $rol->id_rol, // 🔥 aquí se asigna el rol desde la tabla rol
        ]);
    }

    public static function listarUsuarios()
    {
        return User::get();
    }

    public static function obtenerUsuario($id_usuario)
    {
        return User::find($id_usuario);
    }

    public static function actualizarUsuario($camposActualizados, $id_usuario)
    {
        $usuario = User::find($id_usuario);

        if (!$usuario) {
            return null;
        }

        $usuario->update($camposActualizados);
        return $usuario->fresh();
    }

    public static function eliminarUsuario($id_usuario)
    {
        $usuario = User::find($id_usuario);

        if (!$usuario) {
            return null;
        }

        $usuario->delete();
        return true;
    }
}

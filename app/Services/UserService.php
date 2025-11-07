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
    // Si el request trae un id_rol, lo usamos. Si no, buscamos el rol "Usuario"
    $rol_id = $registroUsuario['id_rol'] ?? Rol::where('nombre_rol', 'Usuario')->value('id_rol');

    if (!$rol_id) {
        throw new \Exception('No se encontró un rol válido para el usuario.');
    }

    // Crear usuario con rol asignado
    return user::create([
        'nombres' => $registroUsuario['nombres'],
        'apellidos' => $registroUsuario['apellidos'],
        'correo' => $registroUsuario['correo'],
        'contraseña' => $registroUsuario['contraseña'],
        'id_rol' => $rol_id,
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

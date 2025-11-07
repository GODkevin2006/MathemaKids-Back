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
        return User::create($registroUsuario);
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
        //para que cuando yo haga un delete no me borre los datos y solo cambie el campo de estado a inactivo
        $usuario->estado = $usuario->estado === 'activo' ? 'inactivo' : 'activo';
        $usuario->save();
        return true;
    }
}

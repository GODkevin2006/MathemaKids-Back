<?php

namespace App\Services;
use App\Models\Rol;

class RolService{
    public static function crearRol($registroRol){
        return rol::create($registroRol);
    }


    public static function listarRoles(){
        return rol::get();
    }

    public static function obtenerRol($id_rol){
        $rol = rol::find($id_rol);

        if(!$rol)
        {
            return null;
        }

        return $rol;
    }

    public static function actualizarRol($camposActualizados,$id_rol){
        $rol = rol::find($id_rol);

        if(!$rol) {
            return null;
        }

        $rol->update($camposActualizados);

        return $rol->fresh();
        }

    public static function eliminarRol($id_rol){
        $rol = rol::find($id_rol);

        if(!$rol){
            return null;
        }

        $rol->delete();

        return true;
        }
}
<?php

namespace App\Services;

use App\Models\Publicacion;

class PublicacionService
{
    public static function crearPublicacion($registrarPublicacion){
        return publicacion::create($registrarPublicacion);
    }

    public static function listarPublicaciones(){
        return publicacion::get();
    }

    public static function obtenerPublicacion($id_publicacion){
        $publicacion = publicacion::find($id_publicacion);

        if(!$publicacion)
        {
            return null;
        }

        return $publicacion;
    }

    public static function actualizarPublicacion($camposActualizados, $id_publicacion)
    {
        $publicacion = publicacion::find($id_publicacion);

        if(!$publicacion)
        {
            return null;
        }

        $publicacion->update($camposActualizados);
        return $publicacion;
    }

    public static function eliminarPublicacion($id_publicacion){
        $publicacion = publicacion::find($id_publicacion);

        if(!$publicacion)
        {
            return null;
        }

        $publicacion->delete();

        return true;
    }


}

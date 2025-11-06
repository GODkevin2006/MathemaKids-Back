<?php

namespace App\Services;
use App\Models\Proyecto;

class ProyectoService{
    public static function crearProyecto($registroProyecto){
        return proyecto::create($registroProyecto);
    }


    public static function listarProyecto(){
        return proyecto::get();
    }

    public static function obtenerProyecto($id_proyecto){
        $proyecto = proyecto::find($id_proyecto);

        if(!$proyecto)
        {
            return null;
        }

        return $proyecto;
    }

    public static function actualizarProyecto($camposActualizados,$id_proyecto){
        $proyecto = proyecto::find($id_proyecto);

        if(!$proyecto) {
            return null;
        }

        $proyecto->update($camposActualizados);

        return $proyecto->fresh();
        }

    public static function eliminarProyecto($id_proyecto){
        $proyecto = proyecto::find($id_proyecto);

        if(!$proyecto){
            return null;
        }

        $proyecto->delete();

        return true;
        }
}